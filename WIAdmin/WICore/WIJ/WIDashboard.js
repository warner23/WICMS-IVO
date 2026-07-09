"use strict";

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WIKitchenCompli / WICOS
| File: /WIAdmin/WICore/WIJ/WIDashboard.js
| Type: Dashboard Controller
| Layer: Admin UI Controller
|--------------------------------------------------------------------------
|
| Purpose:
| Real dashboard controller for the admin compliance dashboard.
| - Loads widgets on page load
| - Loads widgets on user actions
| - Handles dependent business/site scope
| - Handles widget drawers
| - Handles customise mode
|
| Rules:
| - Uses WICompliance helper only
| - No direct hardcoded AJAX path here
| - No business logic here
|--------------------------------------------------------------------------
*/

(function (window, document, $) {
    "use strict";

    var WIDashboard = {
        selectors: {
            root: "#wiAdminDashboard",
            businessId: "#wiDashboardBusinessId",
            siteId: "#wiDashboardSiteId",
            dateRange: "#wiDashboardDateRange",
            scopeText: "#wiDashboardScopeText",
            refreshBtn: "#wiDashboardRefreshBtn",
            customiseBtn: "#wiDashboardCustomizeBtn",
            doneBtn: "#wiDashboardDoneBtn",
            resetBtn: "#wiDashboardResetBtn",
            widget: ".wi-dashboard-widget",
            widgetBody: ".wi-dashboard-widget__body",
            widgetDrawer: ".wi-dashboard-widget__drawer",
            widgetToggle: ".wi-dashboard-widget__toggle",
            widgetDrag: ".wi-dashboard-widget__drag",
            zoneWidgets: "[data-zone-widgets]"
        },

        state: {
            editMode: false,
            businessId: 0,
            siteId: 0,
            dateRange: "7_days"
        },

        init: function () {
            var root = document.querySelector(this.selectors.root);
            if (!root) {
                return;
            }

            this.cacheState(root);
            this.bindEvents();
            this.updateScopeText();
            this.loadAllWidgets();
        },

        cacheState: function (root) {
            this.state.businessId = parseInt(root.getAttribute("data-current-business-id") || "0", 10) || 0;
            this.state.siteId = parseInt(root.getAttribute("data-current-site-id") || "0", 10) || 0;

            var businessSelect = document.querySelector(this.selectors.businessId);
            var siteSelect = document.querySelector(this.selectors.siteId);
            var dateRangeSelect = document.querySelector(this.selectors.dateRange);

            if (businessSelect) {
                this.state.businessId = parseInt(businessSelect.value || "0", 10) || 0;
            }

            if (siteSelect) {
                this.state.siteId = parseInt(siteSelect.value || "0", 10) || 0;
            }

            if (dateRangeSelect) {
                this.state.dateRange = String(dateRangeSelect.value || "7_days");
            }
        },

        bindEvents: function () {
            var self = this;

            $(document)
                .off("change.wiDashboardBusiness", this.selectors.businessId)
                .on("change.wiDashboardBusiness", this.selectors.businessId, function () {
                    self.state.businessId = parseInt(this.value || "0", 10) || 0;
                    self.handleBusinessChange();
                });

            $(document)
                .off("change.wiDashboardSite", this.selectors.siteId)
                .on("change.wiDashboardSite", this.selectors.siteId, function () {
                    self.state.siteId = parseInt(this.value || "0", 10) || 0;
                    self.updateScopeText();
                    self.loadAllWidgets();
                });

            $(document)
                .off("change.wiDashboardDateRange", this.selectors.dateRange)
                .on("change.wiDashboardDateRange", this.selectors.dateRange, function () {
                    self.state.dateRange = String(this.value || "7_days");
                    self.loadAllWidgets();
                });

            $(document)
                .off("click.wiDashboardRefresh", this.selectors.refreshBtn)
                .on("click.wiDashboardRefresh", this.selectors.refreshBtn, function () {
                    self.loadAllWidgets();
                });

            $(document)
                .off("click.wiDashboardCustomise", this.selectors.customiseBtn)
                .on("click.wiDashboardCustomise", this.selectors.customiseBtn, function () {
                    self.enableEditMode();
                });

            $(document)
                .off("click.wiDashboardDone", this.selectors.doneBtn)
                .on("click.wiDashboardDone", this.selectors.doneBtn, function () {
                    self.disableEditMode(true);
                });

            $(document)
                .off("click.wiDashboardReset", this.selectors.resetBtn)
                .on("click.wiDashboardReset", this.selectors.resetBtn, function () {
                    self.resetLayout();
                });

            $(document)
                .off("click.wiDashboardToggle", this.selectors.widgetToggle)
                .on("click.wiDashboardToggle", this.selectors.widgetToggle, function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var widget = this.closest(self.selectors.widget);
                    if (!widget) {
                        return;
                    }

                    self.toggleWidgetDrawer(widget);
                });

            $(document)
                .off("click.wiDashboardWidget", this.selectors.widget)
                .on("click.wiDashboardWidget", this.selectors.widget, function (event) {
                    if ($(event.target).closest(self.selectors.widgetToggle).length) {
                        return;
                    }

                    self.navigateToTargetTab(this);
                });
        },

        getScopePayload: function () {
            return {
                business_id: this.state.businessId,
                site_id: this.state.siteId,
                date_range: this.state.dateRange
            };
        },

        handleBusinessChange: function () {
            var self = this;

            this.state.siteId = 0;
            this.loadSitesByBusiness().then(function () {
                self.updateScopeText();
                self.loadAllWidgets();
            });
        },

        loadSitesByBusiness: function () {
            var self = this;
            var siteSelect = document.querySelector(this.selectors.siteId);

            if (!siteSelect) {
                return Promise.resolve();
            }

            siteSelect.innerHTML = '<option value="0">Loading sites…</option>';

            return WICompliance.get("getComplianceDashboardSites", {
                business_id: self.state.businessId
            }).then(function (response) {
                siteSelect.innerHTML = '<option value="0">All Sites</option>';

                if (!response || response.success === false) {
                    WICompliance.showMessage(response && response.message ? response.message : "Failed to load sites.", "error");
                    return;
                }

                var sites = Array.isArray(response.sites) ? response.sites : [];

                sites.forEach(function (site) {
                    var option = document.createElement("option");
                    option.value = String(parseInt(site.site_id || 0, 10) || 0);
                    option.textContent = String(site.site_name || "Site");
                    siteSelect.appendChild(option);
                });

                self.state.siteId = 0;
            });
        },

        loadAllWidgets: function () {
            var self = this;
            var widgets = document.querySelectorAll(this.selectors.widget);

            if (!widgets.length) {
                return;
            }

            widgets.forEach(function (widget) {
                self.loadWidget(widget);
            });

            this.updateScopeText();
        },

        loadWidget: function (widget) {
            var self = this;
            var widgetKey = widget.getAttribute("data-widget-key") || "";
            var body = widget.querySelector(this.selectors.widgetBody);
            var drawer = widget.querySelector(this.selectors.widgetDrawer);

            if (!widgetKey || !body) {
                return;
            }

            body.innerHTML = '<div class="wi-comp-empty">Loading widget…</div>';

            if (drawer) {
                drawer.style.display = "none";
                drawer.innerHTML = "";
            }

            WICompliance.get("getComplianceDashboardWidget", $.extend({}, this.getScopePayload(), {
                widget_key: widgetKey
            })).then(function (response) {
                if (!response || response.success === false) {
                    body.innerHTML = '<div class="alert alert-danger">Failed to load widget.</div>';
                    return;
                }

                body.innerHTML = String(response.html || '<div class="wi-comp-empty">No widget data available.</div>');

                if (response.status_class) {
                    widget.setAttribute("data-status-class", String(response.status_class));
                }

                if (response.drawer_html && drawer) {
                    drawer.innerHTML = String(response.drawer_html);
                }

                self.decorateLoadedWidget(widget);
            });
        },

        decorateLoadedWidget: function (widget) {
            widget.setAttribute("data-loaded", "1");

            $(widget)
                .find("[data-target-tab]")
                .off("click.wiDashboardTargetTab")
                .on("click.wiDashboardTargetTab", function (event) {
                    var targetTab = String($(this).attr("data-target-tab") || "");
                    if (!targetTab) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    var tabTrigger = document.querySelector('[data-toggle="tab"][href="#' + targetTab + '"], [data-bs-toggle="tab"][href="#' + targetTab + '"]');
                    if (tabTrigger) {
                        if (window.jQuery && typeof window.jQuery(tabTrigger).tab === "function") {
                            window.jQuery(tabTrigger).tab("show");
                        } else {
                            tabTrigger.click();
                        }
                    }
                });
        },

        toggleWidgetDrawer: function (widget) {
            var drawer = widget.querySelector(this.selectors.widgetDrawer);
            if (!drawer) {
                return;
            }

            var visible = drawer.style.display === "block";
            drawer.style.display = visible ? "none" : "block";
        },

        navigateToTargetTab: function (widget) {
            var targetTab = widget.getAttribute("data-target-tab") || "";
            if (!targetTab) {
                return;
            }

            var tabLink = document.querySelector('[data-target="#' + targetTab + '"], a[href="#' + targetTab + '"]');
            if (tabLink && typeof tabLink.click === "function") {
                tabLink.click();
                return;
            }

            window.location.hash = targetTab;
        },

        updateScopeText: function () {
            var businessText = "All Businesses";
            var siteText = "All Sites";
            var dateText = "Last 7 Days";

            var businessSelect = document.querySelector(this.selectors.businessId);
            var siteSelect = document.querySelector(this.selectors.siteId);
            var dateRangeSelect = document.querySelector(this.selectors.dateRange);
            var scopeText = document.querySelector(this.selectors.scopeText);

            if (!scopeText) {
                return;
            }

            if (businessSelect && businessSelect.selectedIndex >= 0) {
                businessText = businessSelect.options[businessSelect.selectedIndex].text;
            }

            if (siteSelect && siteSelect.selectedIndex >= 0) {
                siteText = siteSelect.options[siteSelect.selectedIndex].text;
            }

            if (dateRangeSelect && dateRangeSelect.selectedIndex >= 0) {
                dateText = dateRangeSelect.options[dateRangeSelect.selectedIndex].text;
            }

            scopeText.textContent = businessText + " / " + siteText + " / " + dateText;
        },

        enableEditMode: function () {
            this.state.editMode = true;
            document.body.classList.add("wi-dashboard-edit-mode");

            var doneBtn = document.querySelector(this.selectors.doneBtn);
            var customiseBtn = document.querySelector(this.selectors.customiseBtn);
            var drags = document.querySelectorAll(this.selectors.widgetDrag);

            if (doneBtn) {
                doneBtn.style.display = "inline-block";
            }

            if (customiseBtn) {
                customiseBtn.style.display = "none";
            }

            drags.forEach(function (drag) {
                drag.style.display = "inline-block";
            });
        },

        disableEditMode: function (saveLayout) {
            this.state.editMode = false;
            document.body.classList.remove("wi-dashboard-edit-mode");

            var doneBtn = document.querySelector(this.selectors.doneBtn);
            var customiseBtn = document.querySelector(this.selectors.customiseBtn);
            var drags = document.querySelectorAll(this.selectors.widgetDrag);

            if (doneBtn) {
                doneBtn.style.display = "none";
            }

            if (customiseBtn) {
                customiseBtn.style.display = "inline-block";
            }

            drags.forEach(function (drag) {
                drag.style.display = "none";
            });

            if (saveLayout) {
                this.saveLayout();
            }
        },

        collectLayout: function () {
            var layout = {};

            document.querySelectorAll(this.selectors.zoneWidgets).forEach(function (zone) {
                var zoneKey = zone.getAttribute("data-zone-widgets") || "";
                if (!zoneKey) {
                    return;
                }

                layout[zoneKey] = [];

                zone.querySelectorAll(".wi-dashboard-widget").forEach(function (widget) {
                    var widgetKey = widget.getAttribute("data-widget-key") || "";
                    if (widgetKey) {
                        layout[zoneKey].push(widgetKey);
                    }
                });
            });

            return layout;
        },

        saveLayout: function () {
            var payload = $.extend({}, this.getScopePayload(), {
                layout: this.collectLayout()
            });

            WICompliance.post("saveComplianceDashboardLayout", payload).then(function (response) {
                if (!response || response.success === false) {
                    WICompliance.showMessage(response && response.message ? response.message : "Failed to save layout.", "error");
                    return;
                }

                WICompliance.showMessage("Dashboard layout saved.", "success");
            });
        },

        resetLayout: function () {
            var self = this;

            WICompliance.post("resetComplianceDashboardLayout", this.getScopePayload()).then(function (response) {
                if (!response || response.success === false) {
                    WICompliance.showMessage(response && response.message ? response.message : "Failed to reset layout.", "error");
                    return;
                }

                self.loadAllWidgets();
                WICompliance.showMessage("Dashboard layout reset.", "success");
            });
        }
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            WIDashboard.init();
        });
    } else {
        WIDashboard.init();
    }

    window.WIDashboard = WIDashboard;
})(window, document, jQuery);