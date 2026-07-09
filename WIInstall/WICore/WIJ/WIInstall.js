(function ($) {
    'use strict';

    var WIInstall = window.WIInstall || {};
    window.WIInstall = WIInstall;

    WIInstall.state = {
        step: 1,
        requirementsPass: false,
        databasePass: false
    };

    WIInstall.randomHex = function (bytes) {
        var length = (parseInt(bytes, 10) || 24) * 2;
        var chars = 'abcdef0123456789';
        var out = '';
        if (window.crypto && window.crypto.getRandomValues) {
            var arr = new Uint8Array(length);
            window.crypto.getRandomValues(arr);
            for (var i = 0; i < arr.length; i++) {
                out += chars[arr[i] % chars.length];
            }
            return out;
        }
        for (var j = 0; j < length; j++) {
            out += chars[Math.floor(Math.random() * chars.length)];
        }
        return out;
    };

    WIInstall.booleanText = function (value) {
        return value ? 'true' : 'false';
    };

    WIInstall.parseAjaxResponse = function (payload) {
        if (payload && typeof payload === 'object') {
            return payload;
        }
        if (typeof payload === 'string') {
            var trimmed = $.trim(payload);
            if (!trimmed) {
                return { status: 'error', message: 'The installer returned an empty response.' };
            }
            try {
                return JSON.parse(trimmed);
            } catch (e) {
                return { status: 'error', message: trimmed };
            }
        }
        return { status: 'error', message: 'The installer returned an unexpected response.' };
    };

    WIInstall.isInstallSuccess = function (res) {
        return !!res && (res.status === 'install_completed' || res.status === 'success' || res.status === 'completed' || res.status === true || res.outcome === true);
    };

    WIInstall.safeMessage = function (fallback, payload) {
        if (payload && typeof payload.message === 'string' && $.trim(payload.message)) {
            return payload.message;
        }
        if (typeof payload === 'string' && $.trim(payload)) {
            return payload;
        }
        return fallback;
    };

    WIInstall.cookieOnlyValue = function () {
        return $('#cookieonly').is(':checked') ? '1' : '0';
    };

    WIInstall.normalisedDbHost = function () {
        var host = $.trim($('#host').val());
        var port = $.trim($('#db_port').val());
        if (port && port !== '3306' && host.indexOf(';port=') === -1) {
            host += ';port=' + port;
        }
        return host;
    };

    WIInstall.progressText = function (step) {
        var labels = {
            1: 'Welcome — start the guided installation.',
            2: 'Requirements — checking server readiness.',
            3: 'Database — connect and prepare your database.',
            4: 'Site setup — configure site and admin details.',
            5: 'Security — configure salt, sessions and protection.',
            6: 'Install — ready to write config and database tables.',
            7: 'Complete — installation finished.'
        };
        return labels[step] || labels[1];
    };

    WIInstall.goToStep = function (step) {
        step = parseInt(step, 10);
        if (!step || step < 1 || step > 7) return;

        WIInstall.state.step = step;
        $('.wi-step-panel').removeClass('is-active');
        $('.wi-step-panel[data-step="' + step + '"]').addClass('is-active');
        $('[data-step-item]').removeClass('is-active is-complete').each(function () {
            var itemStep = parseInt($(this).attr('data-step-item'), 10);
            if (itemStep === step) $(this).addClass('is-active');
            if (itemStep < step) $(this).addClass('is-complete');
        });

        var pct = Math.round((step / 7) * 100);
        $('#progressFraction').text(step + '/7');
        $('#progressBar').css('width', pct + '%');
        $('.wi-progress-ring').css('background', 'conic-gradient(var(--wi-green) ' + pct + '%, rgba(166,196,226,.18) 0)');
        $('#progressText').text(WIInstall.progressText(step));

        if (step === 2) WIInstall.Requirements();
        if (step === 6) { WIInstall.renderSummary(); WIInstall.resetInstallTimeline(); }
    };

    WIInstall.setRequirement = function (selector, label, pass) {
        var icon = pass ? '<i class="fa fa-check"></i> Pass' : '<i class="fa fa-times"></i> Needs attention';
        $(selector).removeClass('pass fail').addClass(pass ? 'pass' : 'fail').html('<span>' + label + '</span><em>' + icon + '</em>');
    };

    WIInstall.Requirements = function () {
        $('#requirementsAlert').addClass('wi-hidden').removeClass('wi-alert-error wi-alert-success').text('');
        $('#requirementsList li').removeClass('pass fail').find('em').text('Checking');

        $.ajax({
            url: 'WICore/WIClass/WIAjax.php',
            type: 'POST',
            data: { action: 'requirements' },
            success: function (requirements) {
                var res = WIInstall.parseAjaxResponse(requirements);

                var PHPVersion = !!res.PHP_Version;
                var PDOExt = !!res.PDO_Extension;
                var MySQL = !!res.PDO_MySQL_Extension;
                var CURL = !!res.PHP_Curl;
                var Writeable = !!res.WICMS_Folder;

                WIInstall.setRequirement('#requirements-php-version', 'PHP version', PHPVersion);
                WIInstall.setRequirement('#requirements-pdo', 'PDO extension', PDOExt);
                WIInstall.setRequirement('#requirements-mysql', 'PDO MySQL extension', MySQL);
                WIInstall.setRequirement('#requirements-curl', 'cURL extension', CURL);
                WIInstall.setRequirement('#requirements-write', 'WICMS folder writable', Writeable);

                WIInstall.state.requirementsPass = PHPVersion && PDOExt && MySQL && CURL && Writeable;
                $('#requirementsNext').prop('disabled', !WIInstall.state.requirementsPass);

                if (WIInstall.state.requirementsPass) {
                    $('#requirementsAlert').removeClass('wi-hidden wi-alert-error').addClass('wi-alert-success').html('<i class="fa fa-check"></i> Requirements passed. You can continue.');
                } else {
                    $('#requirementsAlert').removeClass('wi-hidden wi-alert-success').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> One or more requirements need attention before installation.');
                }
            },
            error: function () {
                $('#requirementsAlert').removeClass('wi-hidden wi-alert-success').addClass('wi-alert-error').text('Could not run requirements check.');
            }
        });
    };

    WIInstall.validateRequired = function (fields) {
        var ok = true;
        fields.forEach(function (selector) {
            var el = $(selector);
            if (!$.trim(el.val())) {
                el.addClass('wi-field-error');
                ok = false;
            } else {
                el.removeClass('wi-field-error');
            }
        });
        return ok;
    };

    WIInstall.DB = function () {
        $('#dbMessage').addClass('wi-hidden').removeClass('wi-alert-success wi-alert-error').text('');
        if (!WIInstall.validateRequired(['#host', '#username', '#db_name'])) {
            $('#dbMessage').removeClass('wi-hidden').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> Host, username and database name are required.');
            return;
        }

        var btn = $('#testDatabase');
        btn.prop('disabled', true).html('<i class="fa fa-circle-o-notch fa-spin"></i> Testing database');

        $.ajax({
            url: 'WICore/WIClass/WIAjax.php',
            type: 'POST',
            data: {
                action: 'db_settings',
                host: WIInstall.normalisedDbHost(),
                user: $('#username').val(),
                db: $('#db_name').val(),
                pass: $('#password').val()
            },
            success: function (results) {
                var res = WIInstall.parseAjaxResponse(results);
                if (res.outcome === true || res.status === 'success') {
                    WIInstall.state.databasePass = true;
                    $('#dbMessage').removeClass('wi-hidden wi-alert-error').addClass('wi-alert-success').html('<i class="fa fa-check"></i> Database connection is ready.');
                    setTimeout(function () { WIInstall.goToStep(4); }, 450);
                } else {
                    WIInstall.state.databasePass = false;
                    $('#dbMessage').removeClass('wi-hidden wi-alert-success').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> ' + (res.message || 'Unable to connect to database.'));
                }
            },
            error: function () {
                $('#dbMessage').removeClass('wi-hidden wi-alert-success').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> Database test failed.');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test database & continue');
            }
        });
    };

    WIInstall.renderSummary = function () {
        $('#railEnvironment').text($('#environment_mode option:selected').text());
        var summary = [
            ['Site', $('#website_name').val()],
            ['URL', $('#script').val()],
            ['Database', $('#db_name').val()],
            ['Admin user', $('#admin_username').val()],
            ['Hashing', $('#encryption').val()],
            ['Environment', $('#environment_mode').val()],
            ['HTTPS session', WIInstall.booleanText($('#secure_session').is(':checked'))],
            ['HTTP-only cookies', WIInstall.booleanText($('#session_http_only').is(':checked'))]
        ];
        $('#installSummary').html(summary.map(function (item) {
            return '<div><strong>' + item[0] + '</strong><span>' + $('<div>').text(item[1]).html() + '</span></div>';
        }).join(''));
    };


    WIInstall.installStages = ['validate', 'database', 'schema', 'settings', 'config', 'admin', 'lock'];
    WIInstall.installTimer = null;
    WIInstall.currentInstallStage = 'validate';

    WIInstall.resetInstallTimeline = function () {
        if (WIInstall.installTimer) {
            clearInterval(WIInstall.installTimer);
            WIInstall.installTimer = null;
        }
        $('.wi-install-stage').removeClass('is-running is-complete is-failed');
        $('.wi-install-stage i').attr('class', 'fa fa-circle-o');
        WIInstall.currentInstallStage = 'validate';
    };

    WIInstall.markInstallStage = function (stage, state) {
        var row = $('.wi-install-stage[data-install-stage="' + stage + '"]');
        if (!row.length) return;
        row.removeClass('is-running is-complete is-failed').addClass('is-' + state);
        row.find('i').attr('class', 'fa');
    };

    WIInstall.startInstallTimeline = function () {
        WIInstall.resetInstallTimeline();
        var idx = 0;
        WIInstall.markInstallStage(WIInstall.installStages[idx], 'running');
        WIInstall.currentInstallStage = WIInstall.installStages[idx];

        WIInstall.installTimer = setInterval(function () {
            WIInstall.markInstallStage(WIInstall.installStages[idx], 'complete');
            idx += 1;
            if (idx >= WIInstall.installStages.length) {
                idx = WIInstall.installStages.length - 1;
            }
            WIInstall.currentInstallStage = WIInstall.installStages[idx];
            WIInstall.markInstallStage(WIInstall.installStages[idx], 'running');
        }, 900);
    };

    WIInstall.finishInstallTimeline = function () {
        if (WIInstall.installTimer) {
            clearInterval(WIInstall.installTimer);
            WIInstall.installTimer = null;
        }
        WIInstall.installStages.forEach(function (stage) {
            WIInstall.markInstallStage(stage, 'complete');
        });
    };

    WIInstall.failInstallTimeline = function () {
        if (WIInstall.installTimer) {
            clearInterval(WIInstall.installTimer);
            WIInstall.installTimer = null;
        }
        WIInstall.markInstallStage(WIInstall.currentInstallStage || 'validate', 'failed');
    };

    WIInstall.install = function () {
        $('#results_install').addClass('wi-hidden').removeClass('wi-alert-error wi-alert-success').text('');
        WIInstall.resetInstallTimeline();
        if (!WIInstall.validateRequired(['#website_name', '#domain', '#script', '#admin_username', '#admin_password', '#email_address', '#salt', '#host', '#username', '#db_name'])) {
            $('#results_install').removeClass('wi-hidden').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> Please complete all required fields before installing.');
            return;
        }

        $('#install').addClass('wi-hidden');
        $('#installing').removeClass('wi-hidden');
        $('#runInstall').prop('disabled', true);

        WIInstall.startInstallTimeline();

        var encryption = $('#encryption').val();
        var cost = encryption === 'sha512' ? $('#costing_sha512').val() : $('#bcrypt_cost').val();
        var adminPassword = $('#admin_password').val();

        /*
         * Do not pre-hash the first admin password in the browser.
         *
         * Older installer JS sent SHA512(password), then the PHP installer
         * bcrypt-hashed that SHA512 string. The admin login screen later sends
         * the real/plain password to PHP, so password_verify(realPassword,
         * bcrypt(sha512(realPassword))) can never match.
         *
         * The server-side installer already hashes the password using the
         * selected WICMS algorithm. Sending the submitted password here keeps
         * install and login verification aligned. Use HTTPS in production.
         */
        var adminPass = adminPassword;

        $.ajax({
            url: 'WICore/WIClass/WIAjax.php',
            type: 'POST',
            data: {
                action: 'install_settings',
                name: $('#website_name').val(),
                dom: $('#domain').val(),
                script: $('#script').val(),
                session_secure: WIInstall.booleanText($('#secure_session').is(':checked')),
                http: WIInstall.booleanText($('#session_http_only').is(':checked')),
                session_regenerate: WIInstall.booleanText($('#session_regenerate').is(':checked')),
                cookieonly: WIInstall.cookieOnlyValue(),
                login_fingerprint: WIInstall.booleanText($('#login_fingerprint').is(':checked')),
                max_login_attempts: $('#max_login_attempts').val(),
                redirect_after_login: $('#redirect_after_login').val(),
                encryption: encryption,
                cost: cost,
                mailer: $('#mailer').val(),
                db_host: WIInstall.normalisedDbHost(),
                db_name: $('#db_name').val(),
                db_password: $('#password').val(),
                db_username: $('#username').val(),
                bootstrap_version: $('#bootstrap_version').val(),
                salt: $('#salt').val(),
                admin_password: adminPass,
                admin_username: $('#admin_username').val(),
                email_address: $('#email_address').val(),
                environment_mode: $('#environment_mode').val(),
                security_key: $('#security_key').val(),
                cookie_prefix: $('#cookie_prefix').val(),
                session_prefix: $('#session_prefix').val(),
                session_timeout: $('#session_timeout').val(),
                allow_demo_data: WIInstall.booleanText($('#allow_demo_data').is(':checked'))
            },
            success: function (results) {
                var res = WIInstall.parseAjaxResponse(results);
                if (WIInstall.isInstallSuccess(res)) {
                    WIInstall.finishInstallTimeline();
                    $('#results_install').removeClass('wi-hidden wi-alert-error').addClass('wi-alert-success').html('<i class="fa fa-check"></i> Installation completed. Moving to the complete screen...');
                    setTimeout(function () { WIInstall.goToStep(7); }, 450);
                } else {
                    WIInstall.failInstallTimeline();
                    $('#results_install').removeClass('wi-hidden wi-alert-success').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> ' + WIInstall.safeMessage('Install failed.', res));
                }
            },
            error: function (xhr) {
                WIInstall.failInstallTimeline();
                var message = 'Install request failed.';
                if (xhr && xhr.responseText) {
                    try { message = JSON.parse(xhr.responseText).message || message; } catch (e) { message = xhr.responseText; }
                }
                $('#results_install').removeClass('wi-hidden wi-alert-success').addClass('wi-alert-error').html('<i class="fa fa-warning"></i> ' + message);
            },
            complete: function () {
                $('#install').removeClass('wi-hidden');
                $('#installing').addClass('wi-hidden');
                if (WIInstall.state.step !== 7) {
                    $('#runInstall').prop('disabled', false);
                }
            }
        });
    };

    $(document).on('click', '[data-next]', function () {
        var target = parseInt($(this).attr('data-next'), 10);
        if (target === 5 && !WIInstall.validateRequired(['#website_name', '#domain', '#script', '#admin_username', '#admin_password', '#email_address'])) return;
        WIInstall.goToStep(target);
    });

    $(document).on('click', '[data-prev]', function () {
        WIInstall.goToStep($(this).attr('data-prev'));
    });

    $(document).on('click', '#runRequirements', function () { WIInstall.Requirements(); });
    $(document).on('click', '#testDatabase', function () { WIInstall.DB(); });
    $(document).on('click', '#runInstall', function () { WIInstall.install(); });

    $(document).on('click', '[data-random-target]', function () {
        var target = $(this).attr('data-random-target');
        var bytes = $(this).attr('data-random-bytes') || 24;
        $(target).val(WIInstall.randomHex(bytes));
    });

    $(document).on('click', '#wiRegenerateAll', function () {
        $('#salt').val(WIInstall.randomHex(24));
        $('#security_key').val(WIInstall.randomHex(32));
    });

    $(document).on('click', '[data-toggle-password]', function () {
        var target = $($(this).attr('data-toggle-password'));
        var type = target.attr('type') === 'password' ? 'text' : 'password';
        target.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $(document).on('change', '#environment_mode', function () {
        var mode = $(this).val();
        $('#railEnvironment').text($('#environment_mode option:selected').text());
        if (mode === 'production') {
            $('#secure_session, #session_http_only, #session_regenerate, #cookieonly').prop('checked', true);
        }
    });

    $(function () {
        WIInstall.goToStep(1);
    });
})(jQuery);
