/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Ecosystem
| Project: Admin Plugin Marketplace
| File: WIPlugin.js
| Location: /WIAdmin/WICore/WIJ/WIPlugin.js
| Type: Admin JavaScript
| Layer: UI Behaviour Only
| Purpose Area: Plugin Marketplace Cards, Filters, Progress and Contrast Toggle
| Version: 2.4.0
| Created: 2026-03-13
| Last Updated: 2026-06-03
| Status: Production Ready - Compliance Dashboard Theme + WILabs Commercial Flow
|--------------------------------------------------------------------------
*/

var WIPlugin = {
    progressTimers: {},

    init: function () {
        WIPlugin.applySavedContrast();
        WIPlugin.showStoredNotice();
        WIPlugin.activateHashTab();
        WIPlugin.bindEvents();

        $('.wi-plugin-filters').each(function () {
            WIPlugin.applyFilters($(this));
        });

        $(window).on('hashchange', function () {
            WIPlugin.activateHashTab();
        });
    },

    bindEvents: function () {
        $(document)
            .off('click.wipluginAction', '.wi-plugin-action')
            .on('click.wipluginAction', '.wi-plugin-action', function () {
                var $button = $(this);
                var action = String($button.data('plugin-action') || '');
                var slug = String($button.data('plugin-slug') || '');

                if (!slug || !action) {
                    WIPlugin.notice('Plugin action is missing required data.', 'error');
                    return;
                }

                if (action === 'wilabs') {
                    WIPlugin.openWilabsDownload($button, slug);
                    return;
                }

                WIPlugin.runCardAction($button.closest('.wi-plugin-card'), action, slug);
            })
            .off('click.wipluginDetails', '.wi-plugin-details-toggle')
            .on('click.wipluginDetails', '.wi-plugin-details-toggle', function () {
                var $details = $(this).closest('.wi-plugin-card').find('.wi-plugin-card__details').first();
                var visible = !$details.prop('hidden');
                $details.prop('hidden', visible);
                $(this).text(visible ? 'Details' : 'Hide Details');
            })
            .off('input.wipluginFilter change.wipluginFilter', '.wi-plugin-filter-input')
            .on('input.wipluginFilter change.wipluginFilter', '.wi-plugin-filter-input', function () {
                WIPlugin.applyFilters($(this).closest('.wi-plugin-filters'));
            })
            .off('click.wipluginFilterReset', '.wi-plugin-filter-reset')
            .on('click.wipluginFilterReset', '.wi-plugin-filter-reset', function () {
                var $filters = $(this).closest('.wi-plugin-filters');
                $filters.find('[data-plugin-filter="search"]').val('');
                $filters.find('select').val('all');
                WIPlugin.applyFilters($filters);
            })
            .off('click.wipluginContrast', '[data-wi-plugin-contrast-toggle]')
            .on('click.wipluginContrast', '[data-wi-plugin-contrast-toggle]', function () {
                WIPlugin.toggleContrast();
            });
    },

    applySavedContrast: function () {
        $('body').addClass('wi-plugin-marketplace-dark');

        if (window.localStorage && window.localStorage.getItem('wi_plugin_marketplace_theme') === 'light') {
            $('body').addClass('wi-plugin-marketplace-light');
        }

        WIPlugin.updateContrastButton();
    },

    toggleContrast: function () {
        var isLight = $('body').toggleClass('wi-plugin-marketplace-light').hasClass('wi-plugin-marketplace-light');

        try {
            window.localStorage.setItem('wi_plugin_marketplace_theme', isLight ? 'light' : 'dark');
        } catch (e) {
        }

        WIPlugin.updateContrastButton();
    },

    updateContrastButton: function () {
        var isLight = $('body').hasClass('wi-plugin-marketplace-light');
        var $button = $('[data-wi-plugin-contrast-toggle]').first();

        if (!$button.length) {
            return;
        }

        $button.attr('aria-pressed', isLight ? 'true' : 'false');
        $button.find('[data-wi-plugin-contrast-label]').text(isLight ? 'Dark mode' : 'Light mode');
    },

    activateHashTab: function () {
        var hash = window.location.hash || '';
        if (!hash) {
            return;
        }

        var $tab = $('.wi-plugin-tabs a[href="' + hash + '"]');
        if ($tab.length && typeof $tab.tab === 'function') {
            $tab.tab('show');
        }
    },

    showStoredNotice: function () {
        var notice = '';

        try {
            notice = window.sessionStorage.getItem('wi_plugin_notice') || '';
            window.sessionStorage.removeItem('wi_plugin_notice');
        } catch (e) {
            notice = '';
        }

        if (notice) {
            WIPlugin.notice(notice, 'success');
        }
    },

    notice: function (message, type) {
        type = type || 'info';
        var cssType = type === 'error' ? 'danger' : type;
        var html = '<div class="alert alert-' + cssType + ' wi-plugin-alert">' +
            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
            WIPlugin.escapeHtml(message || '') +
            '</div>';
        var $target = $('.wi-plugin-hero').first();

        if ($target.length) {
            $target.after(html);
        } else {
            $('body').prepend(html);
        }
    },

    rememberNotice: function (message) {
        try {
            window.sessionStorage.setItem('wi_plugin_notice', message || 'Plugin action completed.');
        } catch (e) {
        }
    },

    csrfToken: function (action) {
        var token = $('input[name="wi_plugin_csrf_' + (action || '') + '"]').first().val() || '';
        return token || $('input[name="csrf_token"]').first().val() || $('meta[name="csrf-token"]').attr('content') || '';
    },

    attachCsrf: function (payload) {
        payload = payload || {};
        var action = payload.action || '';
        var token = WIPlugin.csrfToken(action);

        if (token) {
            payload.csrf_token = token;

            if (action) {
                payload['wi_plugin_csrf_' + action] = token;
            }
        }

        return payload;
    },

    actionMap: {
        install: 'install_plugin',
        uninstall: 'uninstall_plugin',
        enable: 'enable_plugin',
        activate: 'enable_plugin',
        disable: 'disable_plugin'
    },

    stepsForAction: function (action) {
        var steps = {
            install: [
                'Validating plugin manifest...',
                'Checking dependencies...',
                'Preparing database tables...',
                'Installing database updates...',
                'Registering plugin files and metadata...',
                'Completed.'
            ],
            enable: [
                'Checking plugin installation...',
                'Activating plugin...',
                'Registering sidebar and menu links...',
                'Syncing options...',
                'Completed.'
            ],
            disable: [
                'Checking active usage...',
                'Disabling plugin...',
                'Keeping database safely intact...',
                'Completed.'
            ]
        };

        return steps[action] || ['Preparing...', 'Completed.'];
    },

    runCardAction: function ($card, action, slug) {
        var ajaxAction = WIPlugin.actionMap[action] || action;

        WIPlugin.clearCardError($card);
        WIPlugin.startProgress($card, action);
        $card.addClass('wi-plugin-card--busy').find('.wi-plugin-action').prop('disabled', true);

        WIPlugin.request({ action: ajaxAction, plugin: slug }, function (res) {
            if (res && (res.status === 'success' || res.success === true)) {
                WIPlugin.finishProgress($card, res.message || 'Plugin action completed.');
                WIPlugin.rememberNotice(WIPlugin.successMessage(res));

                if (ajaxAction === 'install_plugin' || ajaxAction === 'enable_plugin') {
                    window.location.hash = '#plugin_installed';
                }

                window.setTimeout(function () {
                    window.location.reload();
                }, 450);
                return;
            }

            WIPlugin.failProgress($card, (res && res.message) ? res.message : 'Plugin request failed.', res);
        }, function (message, detail) {
            WIPlugin.failProgress($card, message, detail);
        });
    },

    openWilabsDownload: function ($button, slug) {
        var url = String($button.data('wilabs-url') || '');

        if (url) {
            window.open(url, '_blank', 'noopener');
            return;
        }

        WIPlugin.notice('This plugin is commercial. The WILabs download/licence URL is not configured yet for ' + slug + '.', 'info');
    },

    request: function (payload, ok, bad) {
        payload = payload || {};

        payload = WIPlugin.attachCsrf(payload);

        $.ajax({
            url: 'WICore/WIClass/WIAjax.php',
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (res) {
                if (typeof ok === 'function') {
                    ok(res);
                    return;
                }

                if (res && (res.status === 'success' || res.success === true)) {
                    WIPlugin.rememberNotice(WIPlugin.successMessage(res));
                    window.location.reload();
                    return;
                }

                WIPlugin.notice((res && res.message) ? res.message : 'Plugin request failed.', 'error');
            },
            error: function (xhr) {
                var message = 'Plugin request failed. Check console for details.';
                var detail = xhr ? xhr.responseText : '';

                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                        detail = xhr.responseJSON;
                    } else if (xhr.responseText) {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) {
                            message = parsed.message;
                            detail = parsed;
                        }
                    }
                } catch (e) {
                }

                console.log('WIPlugin AJAX error:', xhr ? xhr.responseText : '');

                if (typeof bad === 'function') {
                    bad(message, detail);
                    return;
                }

                WIPlugin.notice(message, 'error');
            }
        });
    },

    successMessage: function (res) {
        var message = res.message || 'Plugin action completed.';
        var data = res.data || {};
        var extra = '';

        if (data.plugin_name || data.plugin_slug) {
            extra += '\nPlugin: ' + (data.plugin_name || data.plugin_slug);
        }
        if (data.plugin_status) {
            extra += '\nStatus: ' + data.plugin_status;
        }
        if (data.next_step) {
            extra += '\nNext: ' + data.next_step;
        }

        return message + extra;
    },

    startProgress: function ($card, action) {
        var slug = String($card.data('plugin-slug') || Math.random());
        var steps = WIPlugin.stepsForAction(action);

        $card.find('.wi-plugin-card__progress').first().prop('hidden', false);
        WIPlugin.setProgress($card, 0, steps[0], WIPlugin.actionTitle(action));

        if (WIPlugin.progressTimers[slug]) {
            window.clearInterval(WIPlugin.progressTimers[slug]);
        }

        var index = 0;
        WIPlugin.progressTimers[slug] = window.setInterval(function () {
            index += 1;
            var capped = Math.min(index, Math.max(steps.length - 2, 1));
            WIPlugin.setProgress(
                $card,
                Math.min(90, Math.round((capped / Math.max(steps.length - 1, 1)) * 100)),
                steps[capped] || steps[steps.length - 1],
                WIPlugin.actionTitle(action)
            );
        }, 550);
    },

    actionTitle: function (action) {
        if (action === 'enable' || action === 'activate') {
            return 'Activating plugin';
        }
        if (action === 'disable') {
            return 'Disabling plugin';
        }
        return 'Installing plugin';
    },

    setProgress: function ($card, percent, text, title) {
        percent = Math.max(0, Math.min(100, parseInt(percent, 10) || 0));

        $card.find('[data-plugin-progress-title]').text(title || 'Working...');
        $card.find('[data-plugin-progress-percent]').text(percent + '%');
        $card.find('[data-plugin-progress-text]').text(text || 'Working...');
        $card.find('.wi-plugin-progressbar .progress-bar').css('width', percent + '%').attr('aria-valuenow', percent);
    },

    finishProgress: function ($card, message) {
        WIPlugin.clearProgressTimer($card);
        WIPlugin.setProgress($card, 100, message || 'Completed.', 'Completed');
    },

    failProgress: function ($card, message, detail) {
        WIPlugin.clearProgressTimer($card);
        WIPlugin.setProgress($card, 100, 'Stopped because an error occurred.', 'Action failed');
        $card.removeClass('wi-plugin-card--busy').find('.wi-plugin-action').prop('disabled', false);
        WIPlugin.showCardError($card, message, detail);
    },

    clearProgressTimer: function ($card) {
        var slug = String($card.data('plugin-slug') || '');
        if (slug && WIPlugin.progressTimers[slug]) {
            window.clearInterval(WIPlugin.progressTimers[slug]);
            delete WIPlugin.progressTimers[slug];
        }
    },

    showCardError: function ($card, message, detail) {
        var $error = $card.find('.wi-plugin-card__error').first();
        $error.prop('hidden', false);
        $error.find('[data-plugin-error-message]').text(message || 'Plugin action failed.');
        $error.find('[data-plugin-error-detail]').text(typeof detail === 'string' ? detail : JSON.stringify(detail || {}, null, 2));
        WIPlugin.notice(message || 'Plugin action failed.', 'error');
    },

    clearCardError: function ($card) {
        var $error = $card.find('.wi-plugin-card__error').first();
        $error.prop('hidden', true);
        $error.find('[data-plugin-error-message]').text('');
        $error.find('[data-plugin-error-detail]').text('');
    },

    applyFilters: function ($filters) {
        if (!$filters || !$filters.length) {
            return;
        }

        var context = String($filters.data('plugin-filter-context') || '');
        var $grid = $('[data-plugin-grid="' + context + '"]').first();
        var search = String($filters.find('[data-plugin-filter="search"]').val() || '').toLowerCase().trim();
        var status = String($filters.find('[data-plugin-filter="status"]').val() || 'all');
        var type = String($filters.find('[data-plugin-filter="type"]').val() || 'all');
        var pricing = String($filters.find('[data-plugin-filter="pricing"]').val() || 'all');
        var visible = 0;

        $grid.find('[data-plugin-card="1"]').each(function () {
            var $card = $(this);
            var ok = true;

            if (search && String($card.data('plugin-search') || '').indexOf(search) === -1) {
                ok = false;
            }
            if (status !== 'all' && String($card.data('plugin-status') || '') !== status) {
                ok = false;
            }
            if (type !== 'all' && String($card.data('plugin-type') || '') !== type) {
                ok = false;
            }
            if (pricing !== 'all' && String($card.data('plugin-pricing') || '') !== pricing) {
                ok = false;
            }

            $card.toggle(ok);

            if (ok) {
                visible += 1;
            }
        });

        var $empty = $grid.find('.wi-plugin-grid-empty');
        if (!$empty.length) {
            $empty = $('<div class="col-xs-12 wi-plugin-grid-empty"><div class="alert alert-info">No plugins match these filters.</div></div>');
            $grid.append($empty);
        }
        $empty.toggle(visible === 0);
    },

    install: function (plugin) { WIPlugin.request({ action: 'install_plugin', plugin: plugin }); },
    uninstall: function (plugin) { WIPlugin.request({ action: 'uninstall_plugin', plugin: plugin }); },
    enable: function (plugin) { WIPlugin.request({ action: 'enable_plugin', plugin: plugin }); },
    disable: function (plugin) { WIPlugin.request({ action: 'disable_plugin', plugin: plugin }); },

    validateLicense: function (key, slug) {
        WIPlugin.request({ action: 'plugin_validate_license', license_key: key, plugin_slug: slug }, function (res) {
            WIPlugin.notice(res.valid ? 'License valid' : 'License invalid', res.valid ? 'success' : 'error');
        });
    },

    escapeHtml: function (value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
};

$(function () {
    WIPlugin.init();
});
