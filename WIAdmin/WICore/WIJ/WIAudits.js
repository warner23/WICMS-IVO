;(function ($, window, document) {
    "use strict";

    var selectors = {
        root: "[data-audits-root]",
        message: "[data-audit-message]",
        summary: "[data-audit-summary]",
        count: "[data-audit-count]",
        templateTable: "[data-audit-table='templates']",
        runTable: "[data-audit-table='runs']",
        findingsList: "[data-audit-list='findings']",
        actionsList: "[data-audit-list='actions']",
        filters: "[data-audit-filter]"
    };

    var state = {
        initialised: false,
        loading: false,
        payload: {
            data: { templates: [], runs: [], findings: [], actions: [] },
            summary: {},
            lookups: { sites: [] },
            context: {}
        }
    };

    function root() {
        return $(selectors.root).first();
    }

    function api() {
        return window.WICompliance || null;
    }

    function escapeHtml(value) {
        if (api() && typeof api().escapeHtml === "function") {
            return api().escapeHtml(value);
        }

        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function showMessage(message, type) {
        var $message = root().find(selectors.message);
        $message
            .removeClass("is-success is-error is-warning is-info")
            .addClass("is-" + (type || "info"))
            .text(String(message || ""))
            .prop("hidden", !message);
    }

    function post(action, payload) {
        if (!api() || typeof api().post !== "function") {
            var d = $.Deferred();
            d.resolve({ success: false, message: "WICompliance AJAX helper is not available." });
            return d.promise();
        }

        return api().post(action, payload || {});
    }

    function collectFilters() {
        var payload = {};

        root().find(selectors.filters).each(function () {
            var $field = $(this);
            payload[String($field.attr("data-audit-filter") || "")] = $field.val();
        });

        payload.org_business_id = root().attr("data-org-business-id") || payload.org_business_id || 0;
        payload.business_id = root().attr("data-business-id") || payload.business_id || payload.org_business_id || 0;

        return payload;
    }

    function unwrap(response) {
        response = response || {};
        var data = response.data || {};

        if (data.data && (data.data.templates || data.data.runs || data.data.findings || data.data.actions)) {
            return data;
        }

        return data;
    }

    function load() {
        if (state.loading || !root().length) {
            return;
        }

        state.loading = true;
        showMessage("Loading audits…", "info");

        post("compliance_audits_load", collectFilters()).done(function (response) {
            state.loading = false;

            if (!response || response.success === false) {
                showMessage(response && response.message ? response.message : "Audits could not be loaded.", "error");
                renderEmpty();
                return;
            }

            state.payload = unwrap(response);
            render();
            showMessage("", "info");
        });
    }

    function render() {
        var payload = state.payload || {};
        var data = payload.data || {};

        renderLookups(payload.lookups || {});
        renderSummary(payload.summary || {});
        renderTemplates(data.templates || []);
        renderRuns(data.runs || []);
        renderFindings(data.findings || []);
        renderActions(data.actions || []);
    }

    function renderEmpty() {
        renderSummary({});
        root().find(selectors.templateTable).html('<tr><td colspan="7">No audits found.</td></tr>');
        root().find(selectors.runTable).html('<tr><td colspan="7">No audit runs found.</td></tr>');
        root().find(selectors.findingsList).html('<p class="wi-audit-empty">No findings.</p>');
        root().find(selectors.actionsList).html('<p class="wi-audit-empty">No actions.</p>');
    }

    function renderLookups(lookups) {
        var sites = lookups.sites || [];
        var $site = root().find("[data-audit-filter='site_id']");
        var current = String($site.val() || "0");

        if (!$site.length || $site.data("loaded") === true) {
            return;
        }

        var html = '<option value="0">All sites</option>';
        sites.forEach(function (site) {
            html += '<option value="' + escapeHtml(site.org_site_id || site.site_id || site.id || 0) + '">' + escapeHtml(site.site_name || "Site") + '</option>';
        });

        $site.html(html).val(current).data("loaded", true);
    }

    function renderSummary(summary) {
        summary = summary || {};

        root().find(selectors.summary).each(function () {
            var key = String($(this).attr("data-audit-summary") || "");
            var value = summary[key];
            $(this).text(value === null || typeof value === "undefined" || value === "" ? "—" : value);
        });
    }

    function renderTemplates(rows) {
        root().find("[data-audit-count='templates']").text(rows.length);

        if (!rows.length) {
            root().find(selectors.templateTable).html('<tr><td colspan="7">No audit templates found.</td></tr>');
            return;
        }

        var html = rows.map(function (row) {
            return '<tr data-audit-id="' + escapeHtml(row.id) + '">' +
                '<td><strong>' + escapeHtml(row.title) + '</strong><small>' + escapeHtml(row.audit_code || '') + '</small></td>' +
                '<td>' + escapeHtml(label(row.audit_type)) + '</td>' +
                '<td>' + escapeHtml(row.site_name || 'All sites') + '</td>' +
                '<td><span class="wi-pill wi-pill--' + escapeHtml(row.state || 'grey') + '">' + escapeHtml(label(row.status)) + '</span></td>' +
                '<td>' + escapeHtml(row.due_date || '—') + '</td>' +
                '<td>' + escapeHtml(row.score || '—') + '</td>' +
                '<td class="wi-audit-actions">' +
                    '<button type="button" class="wi-btn wi-btn-small" data-audit-action="view" data-audit-id="' + escapeHtml(row.id) + '">View</button>' +
                    '<button type="button" class="wi-btn wi-btn-small" data-audit-action="edit" data-audit-id="' + escapeHtml(row.id) + '">Edit</button>' +
                    '<button type="button" class="wi-btn wi-btn-small wi-btn-primary" data-audit-action="start" data-audit-id="' + escapeHtml(row.id) + '">Start</button>' +
                '</td>' +
            '</tr>';
        }).join("");

        root().find(selectors.templateTable).html(html);
    }

    function renderRuns(rows) {
        root().find("[data-audit-count='runs']").text(rows.length);

        if (!rows.length) {
            root().find(selectors.runTable).html('<tr><td colspan="7">No audit runs found.</td></tr>');
            return;
        }

        var html = rows.map(function (row) {
            return '<tr data-audit-run-id="' + escapeHtml(row.id) + '">' +
                '<td><strong>' + escapeHtml(row.run_title || row.audit_title || 'Audit run') + '</strong><small>' + escapeHtml(row.audit_title || '') + '</small></td>' +
                '<td>' + escapeHtml(row.site_name || '—') + '</td>' +
                '<td><span class="wi-pill wi-pill--' + escapeHtml(row.status === 'completed' ? 'green' : (row.status === 'in_progress' ? 'amber' : 'grey')) + '">' + escapeHtml(label(row.status)) + '</span></td>' +
                '<td>' + escapeHtml(row.started_at || '—') + '</td>' +
                '<td>' + escapeHtml(row.completed_at || '—') + '</td>' +
                '<td>' + escapeHtml(row.score || '—') + '</td>' +
                '<td class="wi-audit-actions">' +
                    '<button type="button" class="wi-btn wi-btn-small" data-audit-action="view-run" data-audit-id="' + escapeHtml(row.audit_id) + '">View</button>' +
                    (row.status !== 'completed' ? '<button type="button" class="wi-btn wi-btn-small wi-btn-primary" data-audit-action="complete-run" data-run-id="' + escapeHtml(row.id) + '">Complete</button>' : '') +
                '</td>' +
            '</tr>';
        }).join("");

        root().find(selectors.runTable).html(html);
    }

    function renderFindings(rows) {
        root().find("[data-audit-count='findings']").text(rows.length);

        if (!rows.length) {
            root().find(selectors.findingsList).html('<p class="wi-audit-empty">No findings.</p>');
            return;
        }

        root().find(selectors.findingsList).html(rows.map(function (row) {
            return '<article class="wi-audit-list-card">' +
                '<span class="wi-pill wi-pill--' + escapeHtml(row.severity === 'critical' || row.severity === 'high' ? 'red' : 'amber') + '">' + escapeHtml(label(row.severity)) + '</span>' +
                '<strong>' + escapeHtml(row.title || 'Finding') + '</strong>' +
                '<small>' + escapeHtml(row.site_name || '') + ' · ' + escapeHtml(label(row.status)) + '</small>' +
                '<p>' + escapeHtml(row.finding_text || '') + '</p>' +
            '</article>';
        }).join(""));
    }

    function renderActions(rows) {
        root().find("[data-audit-count='actions']").text(rows.length);

        if (!rows.length) {
            root().find(selectors.actionsList).html('<p class="wi-audit-empty">No actions.</p>');
            return;
        }

        root().find(selectors.actionsList).html(rows.map(function (row) {
            return '<article class="wi-audit-list-card">' +
                '<span class="wi-pill wi-pill--' + escapeHtml(row.status === 'completed' ? 'green' : 'amber') + '">' + escapeHtml(label(row.status)) + '</span>' +
                '<strong>' + escapeHtml(row.action_text || 'Action') + '</strong>' +
                '<small>' + escapeHtml(row.site_name || '') + ' · Due ' + escapeHtml(row.due_date || '—') + '</small>' +
            '</article>';
        }).join(""));
    }

    function label(value) {
        return String(value || "").replace(/_/g, " ").replace(/\b\w/g, function (m) { return m.toUpperCase(); });
    }

    function openAuditForm(row) {
        row = row || {};
        var sites = ((state.payload || {}).lookups || {}).sites || [];
        var siteOptions = '<option value="0">All sites</option>' + sites.map(function (site) {
            var id = site.org_site_id || site.site_id || site.id || 0;
            return '<option value="' + escapeHtml(id) + '" ' + (String(row.org_site_id || row.site_id || 0) === String(id) ? 'selected' : '') + '>' + escapeHtml(site.site_name || 'Site') + '</option>';
        }).join("");

        var html = '<form class="wi-audit-form">' +
            '<input type="hidden" id="wiAuditFormId" value="' + escapeHtml(row.id || 0) + '">' +
            '<label>Title<input type="text" id="wiAuditFormTitle" value="' + escapeHtml(row.title || '') + '" required></label>' +
            '<label>Type<select id="wiAuditFormType">' + option('internal', row.audit_type) + option('food_safety', row.audit_type) + option('health_safety', row.audit_type) + option('fire_safety', row.audit_type) + option('equipment', row.audit_type) + option('supplier', row.audit_type) + option('custom', row.audit_type) + '</select></label>' +
            '<label>Site<select id="wiAuditFormSite">' + siteOptions + '</select></label>' +
            '<label>Status<select id="wiAuditFormStatus">' + option('planned', row.status) + option('in_progress', row.status) + option('completed', row.status) + option('overdue', row.status) + option('archived', row.status) + '</select></label>' +
            '<label>Due date<input type="date" id="wiAuditFormDue" value="' + escapeHtml(row.due_date || '') + '"></label>' +
            '<label>Notes<textarea id="wiAuditFormNotes" rows="4">' + escapeHtml(row.notes || '') + '</textarea></label>' +
        '</form>';

        api().openHtmlModal(row.id ? 'Edit Audit' : 'New Audit', html, 'Save Audit', function () {
            saveAudit();
        });
    }

    function option(value, selected) {
        return '<option value="' + escapeHtml(value) + '" ' + (String(value) === String(selected || '') ? 'selected' : '') + '>' + escapeHtml(label(value)) + '</option>';
    }

    function saveAudit() {
        var payload = {
            id: $('#wiAuditFormId').val(),
            title: $('#wiAuditFormTitle').val(),
            audit_type: $('#wiAuditFormType').val(),
            org_site_id: $('#wiAuditFormSite').val(),
            site_id: $('#wiAuditFormSite').val(),
            status: $('#wiAuditFormStatus').val(),
            due_date: $('#wiAuditFormDue').val(),
            notes: $('#wiAuditFormNotes').val(),
            org_business_id: collectFilters().org_business_id,
            business_id: collectFilters().business_id
        };

        post('compliance_audit_save', payload).done(function (response) {
            if (!response || response.success === false) {
                showMessage(response && response.message ? response.message : 'Audit could not be saved.', 'error');
                return;
            }

            if (api().removeModal) {
                api().removeModal();
            }
            load();
        });
    }

    function startAudit(id) {
        var siteId = root().find("[data-audit-filter='site_id']").val() || 0;
        if (!parseInt(siteId, 10)) {
            showMessage('Select a site before starting an audit run.', 'warning');
            return;
        }

        post('compliance_audit_start', {
            audit_id: id,
            site_id: siteId,
            org_site_id: siteId,
            org_business_id: collectFilters().org_business_id
        }).done(function (response) {
            showMessage(response.message || 'Audit start response received.', response.success === false ? 'error' : 'success');
            load();
        });
    }

    function openCompleteRunForm(runId) {
        var html = '<form class="wi-audit-form">' +
            '<input type="hidden" id="wiAuditRunFormId" value="' + escapeHtml(runId) + '">' +
            '<label>Score<input type="number" id="wiAuditRunScore" min="0" max="100" step="0.01" placeholder="0-100"></label>' +
            '<label>Notes<textarea id="wiAuditRunNotes" rows="4"></textarea></label>' +
            '<hr>' +
            '<label>Finding title (optional)<input type="text" id="wiAuditFindingTitle" placeholder="Create finding if something failed"></label>' +
            '<label>Severity<select id="wiAuditFindingSeverity">' + option('medium', 'medium') + option('critical', '') + option('high', '') + option('low', '') + '</select></label>' +
            '<label>Finding notes<textarea id="wiAuditFindingText" rows="3"></textarea></label>' +
        '</form>';

        api().openHtmlModal('Complete Audit Run', html, 'Complete Run', function () {
            completeRun();
        });
    }

    function completeRun() {
        post('compliance_audit_complete', {
            run_id: $('#wiAuditRunFormId').val(),
            score: $('#wiAuditRunScore').val(),
            notes: $('#wiAuditRunNotes').val(),
            finding_title: $('#wiAuditFindingTitle').val(),
            finding_severity: $('#wiAuditFindingSeverity').val(),
            finding_text: $('#wiAuditFindingText').val()
        }).done(function (response) {
            showMessage(response.message || 'Audit complete response received.', response.success === false ? 'error' : 'success');
            if (response && response.success !== false && api().removeModal) {
                api().removeModal();
            }
            load();
        });
    }

    function viewAudit(id) {
        post('compliance_audit_get', { id: id }).done(function (response) {
            if (!response || response.success === false) {
                showMessage(response && response.message ? response.message : 'Audit could not be loaded.', 'error');
                return;
            }

            var data = response.data || {};
            var audit = data.audit || {};
            var html = '<div class="wi-audit-detail">' +
                '<h3>' + escapeHtml(audit.title || 'Audit') + '</h3>' +
                '<p><strong>Type:</strong> ' + escapeHtml(label(audit.audit_type)) + '</p>' +
                '<p><strong>Status:</strong> ' + escapeHtml(label(audit.status)) + '</p>' +
                '<p><strong>Site:</strong> ' + escapeHtml(audit.site_name || 'All sites') + '</p>' +
                '<p><strong>Due:</strong> ' + escapeHtml(audit.due_date || '—') + '</p>' +
                '<p><strong>Score:</strong> ' + escapeHtml(audit.score || '—') + '</p>' +
                '<hr><p>Runs: ' + escapeHtml((data.runs || []).length) + ' · Findings: ' + escapeHtml((data.findings || []).length) + ' · Actions: ' + escapeHtml((data.actions || []).length) + '</p>' +
            '</div>';

            api().openHtmlModal('Audit Details', html, 'Close', function () {
                if (api().removeModal) {
                    api().removeModal();
                }
            });
        });
    }

    function editAudit(id) {
        post('compliance_audit_get', { id: id }).done(function (response) {
            if (!response || response.success === false) {
                showMessage(response && response.message ? response.message : 'Audit could not be loaded.', 'error');
                return;
            }

            openAuditForm((response.data || {}).audit || {});
        });
    }

    function bindEvents() {
        $(document)
            .off('click.wiAudits', '[data-audits-root] [data-audit-action]')
            .on('click.wiAudits', '[data-audits-root] [data-audit-action]', function () {
                var $button = $(this);
                var action = String($button.attr('data-audit-action') || '');
                var id = parseInt($button.attr('data-audit-id') || '0', 10) || 0;

                if (action === 'refresh') {
                    load();
                } else if (action === 'new-audit') {
                    openAuditForm({});
                } else if (action === 'view' || action === 'view-run') {
                    viewAudit(id);
                } else if (action === 'edit') {
                    editAudit(id);
                } else if (action === 'start') {
                    startAudit(id);
                } else if (action === 'complete-run') {
                    openCompleteRunForm(parseInt($button.attr('data-run-id') || '0', 10) || 0);
                }
            });

        $(document)
            .off('change.wiAudits input.wiAudits', '[data-audits-root] [data-audit-filter]')
            .on('change.wiAudits input.wiAudits', '[data-audits-root] [data-audit-filter]', debounce(load, 300));
    }

    function debounce(fn, wait) {
        var timer = null;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn, wait);
        };
    }

    function init() {
        if (!root().length) {
            return;
        }

        if (state.initialised) {
            load();
            return;
        }

        state.initialised = true;
        bindEvents();
        load();
    }

    window.WIComplianceAudits = { init: init, load: load };
    window.WIAuditsTab = window.WIComplianceAudits;
})(jQuery, window, document);

jQuery(function () {
    if (window.WIComplianceAudits && typeof window.WIComplianceAudits.init === 'function') {
        window.WIComplianceAudits.init();
    }
});
