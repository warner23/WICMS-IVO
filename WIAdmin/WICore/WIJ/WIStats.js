"use strict";

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WIKitchenCompli / WICOS
| Project: WI Ecosystem
| File: /root/WIAdmin/WICore/WIJ/WIStats.js
| Type: Tab Controller
| Layer: Admin UI
| Purpose Area: Statistics
| Version: 3.0.0
| Created: 2026-04-21 00:00:00
| Last Updated: 2026-04-21 00:00:00
| Batch: Batch 14 – Admin UI Alignment
| Status: Production Ready
|--------------------------------------------------------------------------
| Summary:
| Statistics tab controller.
| - renders charts
| - refreshes statistics from WICompliance/WIAjax
| - updates summary cards and site table
|--------------------------------------------------------------------------
*/

window.WIStatsTab = (function ($, window, document, WICompliance) {
    var selectors = {
        root: "#wiStatisticsTab",
        message: "#wiStatsMessage",
        refreshButton: "#wiStatsRefreshBtn",
        initialData: "#wiStatsInitialData",

        sitesCount: "#wiStatsSitesCount",
        checklistsCount: "#wiStatsChecklistsCount",
        questionsCount: "#wiStatsQuestionsCount",
        equipmentCount: "#wiStatsEquipmentCount",
        documentsCount: "#wiStatsDocumentsCount",
        trainingCount: "#wiStatsTrainingCount",
        incidentsCount: "#wiStatsIncidentsCount",
        alertsCount: "#wiStatsAlertsCount",

        sitesTableBody: "#wiStatsSitesTableBody",
        highlights: "#wiStatsHighlights",

        areaChart: "#wiStatsAreaChart",
        attentionChart: "#wiStatsAttentionChart",
        priorityChart: "#wiStatsPriorityChart",
        siteStatusChart: "#wiStatsSiteStatusChart"
    };

    var charts = {
        area: null,
        attention: null,
        priority: null,
        siteStatus: null
    };

    function hasRoot() {
        return $(selectors.root).length > 0;
    }

    function canRun() {
        return hasRoot() && !!window.jQuery;
    }

    function escapeHtml(value) {
        if (WICompliance && typeof WICompliance.escapeHtml === "function") {
            return WICompliance.escapeHtml(value);
        }

        return $("<div>").text(value == null ? "" : String(value)).html();
    }

    function showMessage(message, type) {
        var $message = $(selectors.message);
        var safeType = String(type || "info").toLowerCase();
        var cssClass = "alert-info";

        $message.removeClass("alert-info alert-success alert-warning alert-danger");

        if (safeType === "success") {
            cssClass = "alert-success";
        } else if (safeType === "warning") {
            cssClass = "alert-warning";
        } else if (safeType === "error" || safeType === "danger") {
            cssClass = "alert-danger";
        }

        $message
            .addClass(cssClass)
            .html(escapeHtml(message || ""))
            .show();
    }

    function hideMessage() {
        $(selectors.message).hide().removeClass("alert-info alert-success alert-warning alert-danger").text("");
    }

    function safeNumber(value) {
        var number = parseInt(value, 10);
        return isNaN(number) ? 0 : number;
    }

    function getInitialData() {
        var el = document.querySelector(selectors.initialData);

        if (!el) {
            return {};
        }

        try {
            return JSON.parse(el.textContent || "{}");
        } catch (error) {
            return {};
        }
    }

    function destroyChart(chart) {
        if (chart && typeof chart.destroy === "function") {
            chart.destroy();
        }
    }

    function buildDoughnutChart(canvasSelector, labels, values, titleText) {
        var canvas = document.querySelector(canvasSelector);

        if (!canvas || typeof window.Chart === "undefined") {
            return null;
        }

        return new window.Chart(canvas, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [{
                    data: values
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    },
                    title: {
                        display: false,
                        text: titleText || ""
                    }
                }
            }
        });
    }

    function buildBarChart(canvasSelector, labels, values, titleText) {
        var canvas = document.querySelector(canvasSelector);

        if (!canvas || typeof window.Chart === "undefined") {
            return null;
        }

        return new window.Chart(canvas, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: titleText || "",
                    data: values
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: titleText || ""
                    }
                }
            }
        });
    }

    function renderSummary(summary) {
        $(selectors.sitesCount).text(String(safeNumber(summary.sites)));
        $(selectors.checklistsCount).text(String(safeNumber(summary.checklists)));
        $(selectors.questionsCount).text(String(safeNumber(summary.questions)));
        $(selectors.equipmentCount).text(String(safeNumber(summary.equipment)));
        $(selectors.documentsCount).text(String(safeNumber(summary.documents)));
        $(selectors.trainingCount).text(String(safeNumber(summary.training)));
        $(selectors.incidentsCount).text(String(safeNumber(summary.incidents)));
        $(selectors.alertsCount).text(String(safeNumber(summary.alerts)));
    }

    function renderSitesTable(sites) {
        var html = "";

        if (!$.isArray(sites) || !sites.length) {
            html = "<tr class=\"wi-stats-empty-row\"><td colspan=\"4\" class=\"text-center text-muted\">No site data found.</td></tr>";
            $(selectors.sitesTableBody).html(html);
            return;
        }

        $.each(sites, function (_, site) {
            var siteName = String(site.site_name || site.name || "Unnamed Site");
            var isActive = safeNumber(site.is_active) === 1;
            var timezone = String(site.timezone || "—");
            var ipMode = String(site.ip_mode || "off");

            html += ""
                + "<tr>"
                + "  <td>" + escapeHtml(siteName) + "</td>"
                + "  <td><span class=\"" + escapeHtml(isActive ? "label label-success" : "label label-default") + "\">" + escapeHtml(isActive ? "Active" : "Inactive") + "</span></td>"
                + "  <td>" + escapeHtml(timezone) + "</td>"
                + "  <td>" + escapeHtml(ipMode) + "</td>"
                + "</tr>";
        });

        $(selectors.sitesTableBody).html(html);
    }

    function renderHighlights(highlights) {
        var html = "";

        html += "<div><span>Open Alerts</span><strong>" + escapeHtml(safeNumber(highlights.open_alerts)) + "</strong></div>";
        html += "<div><span>Overdue / Missed Alerts</span><strong>" + escapeHtml(safeNumber(highlights.overdue_or_missed_alerts)) + "</strong></div>";
        html += "<div><span>Critical Alerts</span><strong>" + escapeHtml(safeNumber(highlights.critical_alerts)) + "</strong></div>";
        html += "<div><span>Active Sites</span><strong>" + escapeHtml(safeNumber(highlights.active_sites)) + "</strong></div>";
        html += "<div><span>Inactive Sites</span><strong>" + escapeHtml(safeNumber(highlights.inactive_sites)) + "</strong></div>";
        html += "<div><span>Attention Feed Total</span><strong>" + escapeHtml(safeNumber(highlights.attention_feed_total)) + "</strong></div>";

        $(selectors.highlights).html(html);
    }

    function renderCharts(chartData) {
        var area = chartData.area_breakdown || {};
        var attention = chartData.attention_mix || {};
        var priority = chartData.alert_priority_mix || {};
        var siteStatus = chartData.site_status_mix || {};

        destroyChart(charts.area);
        destroyChart(charts.attention);
        destroyChart(charts.priority);
        destroyChart(charts.siteStatus);

        charts.area = buildBarChart(
            selectors.areaChart,
            $.isArray(area.labels) ? area.labels : [],
            $.isArray(area.values) ? area.values : [],
            "Area Breakdown"
        );

        charts.attention = buildDoughnutChart(
            selectors.attentionChart,
            $.isArray(attention.labels) ? attention.labels : [],
            $.isArray(attention.values) ? attention.values : [],
            "Attention Feed Mix"
        );

        charts.priority = buildDoughnutChart(
            selectors.priorityChart,
            $.isArray(priority.labels) ? priority.labels : [],
            $.isArray(priority.values) ? priority.values : [],
            "Alert Priority Mix"
        );

        charts.siteStatus = buildDoughnutChart(
            selectors.siteStatusChart,
            $.isArray(siteStatus.labels) ? siteStatus.labels : [],
            $.isArray(siteStatus.values) ? siteStatus.values : [],
            "Site Status"
        );
    }

    function render(data) {
        var summary = data.summary || {};
        var sites = $.isArray(data.sites) ? data.sites : [];
        var highlights = data.highlights || {};
        var chartData = data.charts || {};

        renderSummary(summary);
        renderSitesTable(sites);
        renderHighlights(highlights);
        renderCharts(chartData);
    }

    function refresh() {
        if (!WICompliance || typeof WICompliance.get !== "function") {
            showMessage("WICompliance helper is not available for refresh.", "warning");
            return $.Deferred().resolve().promise();
        }

        hideMessage();
        $(selectors.refreshButton).prop("disabled", true);

        return WICompliance.get("getStatisticsData", {}).then(function (response) {
            if (!response || response.success === false) {
                showMessage(response && response.message ? response.message : "Failed to load statistics.", "error");
                return;
            }

            render(response.data || {});
            showMessage("Statistics refreshed successfully.", "success");
        }).always(function () {
            $(selectors.refreshButton).prop("disabled", false);
        });
    }

    function bindEvents() {
        $(document)
            .off("click.wiStatsRefresh", selectors.refreshButton)
            .on("click.wiStatsRefresh", selectors.refreshButton, function () {
                refresh();
            });
    }

    function init() {
        var initialData;

        if (!canRun()) {
            return;
        }

        bindEvents();

        initialData = getInitialData();
        render(initialData);
    }

    return {
        init: init,
        refresh: refresh
    };
})(jQuery, window, document, window.WICompliance);

jQuery(function () {
    if (window.WIStatsTab && typeof window.WIStatsTab.init === "function") {
        window.WIStatsTab.init();
    }
});