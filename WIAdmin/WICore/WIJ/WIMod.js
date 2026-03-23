var WIMod = (function ($) {
    "use strict";

    var ajaxUrl = "WICore/WIClass/WIAjax.php";

    function getCsrfToken() {
        var token = $('input[name="csrf_token"]').first().val();

        if (!token) {
            token = $('meta[name="csrf-token"]').attr("content");
        }

        return token || "";
    }

    function request(payload, onSuccess) {
        payload = payload || {};
        payload.csrf_token = getCsrfToken();

        $.ajax({
            url: ajaxUrl,
            type: "POST",
            dataType: "json",
            data: payload,
            success: function (res) {
                console.log("WIMod response:", res);

                if (typeof onSuccess === "function") {
                    onSuccess(res);
                    return;
                }

                if (res && res.status === "success") {
                    window.location.reload();
                    return;
                }

                alert((res && res.message) ? res.message : "Request failed.");
            },
            error: function (xhr) {
                console.log("WIMod AJAX error:", xhr.responseText);
                alert("Module request failed. Check console for details.");
            }
        });
    }

    function loadPage(action, page, targetSelector) {
        request({
            action: action,
            page: page
        }, function (res) {
            if (res.status !== "success") {
                alert(res.message || "Failed to load page.");
                return;
            }

            $(targetSelector).html(res.data.html || "");
        });
    }

    function bindPagination() {
        $(document).on("click", ".wi-module-pager button[data-action]", function (e) {
            e.preventDefault();

            var $button = $(this);
            var action = $button.data("action");
            var page = parseInt($button.data("page"), 10) || 1;
            var target = $button.data("target");

            if (!action || !target) {
                return;
            }

            loadPage(action, page, target);
        });
    }

    function bindSearch() {
        $(document).on("input", ".wi-store-search", function () {
            var $input = $(this);
            var query = $.trim($input.val()).toLowerCase();
            var target = $input.data("target");

            if (!target) {
                return;
            }

            $(target).each(function () {
                var $card = $(this);
                var text = $card.text().toLowerCase();

                if (query === "" || text.indexOf(query) !== -1) {
                    $card.closest('.col-md-4, .col-sm-6, .col-xs-12').show();
                } else {
                    $card.closest('.col-md-4, .col-sm-6, .col-xs-12').hide();
                }
            });
        });
    }

    function init() {
        bindPagination();
        bindSearch();
    }

    return {
        init: init,
        request: request,

        install: function (name, author) {
            request({
                action: "install_module",
                mod_name: name,
                mod_author: author || ""
            });
        },

        uninstall: function (name, author) {
            request({
                action: "uninstall_module",
                mod_name: name,
                mod_author: author || ""
            });
        },

        enable: function (name) {
            request({
                action: "enable_module",
                mod_name: name
            });
        },

        disable: function (name) {
            request({
                action: "disable_module",
                mod_name: name
            });
        },

        installElement: function (name, author) {
            request({
                action: "install_element",
                element_name: name,
                element_author: author || ""
            });
        },

        uninstallElements: function (name, author) {
            request({
                action: "uninstall_element",
                element_name: name,
                element_author: author || ""
            });
        },

        enableElement: function (name) {
            request({
                action: "enable_element",
                element_name: name
            });
        },

        disableElement: function (name) {
            request({
                action: "disable_element",
                element_name: name
            });
        },

        nextModulesPage: function (page) {
            loadPage("NextModPage", page, "#modulesStoreList");
        },

        nextInstalledModulesPage: function (page) {
            loadPage("NextInstalledModulesPage", page, "#installedModulesList");
        },

        nextElementsPage: function (page) {
            loadPage("NextElementsPage", page, "#elementsStoreList");
        },

        nextInstalledElementsPage: function (page) {
            loadPage("NextInstalledElementsPage", page, "#installedElementsList");
        },

        nextBuilderModulesPage: function (page) {
            loadPage("NextBuilderModulesPage", page, "#builderModulesList");
        }
    };
})(jQuery);

jQuery(function () {
    WIMod.init();
});