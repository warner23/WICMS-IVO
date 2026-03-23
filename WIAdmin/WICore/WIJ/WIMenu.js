/***********
** WIMenu NAMESPACE
**************/
var WIMenu = (function ($) {
    "use strict";

    var ajaxUrl = "WICore/WIClass/WIAjax.php";

    function preventEvent(e) {
        if (e && typeof e.preventDefault === "function") {
            e.preventDefault();
        } else if (window.event && typeof window.event.preventDefault === "function") {
            window.event.preventDefault();
        }
    }

    function showLoader() {
        $(".ajax-loading").removeClass("hide").addClass("show");
    }

    function hideLoader() {
        $(".ajax-loading").removeClass("show").addClass("hide");
    }

    function openModal(name) {
        $("#modal-" + name + "-details").removeClass("hide").addClass("show");
    }

    function closeModal(name) {
        $("#modal-" + name + "-details").removeClass("show").addClass("hide");
    }

    function getVisibleModalPrimaryButton() {
        return $(".modal.show .modal-footer .btn-primary").first();
    }

    function parseResponse(result) {
        if (typeof result === "object" && result !== null) {
            return result;
        }

        if (typeof result !== "string") {
            return {};
        }

        try {
            return JSON.parse(result);
        } catch (e) {
            return {
                status: "error",
                message: result
            };
        }
    }

    function normaliseMenuPayload(data) {
        if (data && data.menu) {
            return data.menu;
        }

        return data || {};
    }

    function renderMessage(target, type, message) {
        if (!target || !$(target).length) {
            return;
        }

        var safeType = type === "success" ? "success" : "danger";
        var html = '<div class="alert alert-' + safeType + '">' + (message || "") + "</div>";

        $(target).html(html);
    }

    function clearMessages() {
        $("#mresults, #admresults, #sbmresults").html("");
    }

    function refreshPage() {
        if (typeof WICore !== "undefined" && typeof WICore.Refresh === "function") {
            WICore.Refresh();
            return;
        }

        window.location.reload();
    }

    function post(action, data, options) {
        options = options || {};

        var button = options.button || getVisibleModalPrimaryButton();
        var target = options.target || "";
        var loadingLabel = options.loadingLabel || "Saving";

        clearMessages();
        showLoader();

        if (button && button.length && typeof WICore !== "undefined" && typeof WICore.loadingButton === "function") {
            WICore.loadingButton(button, loadingLabel);
        }

        return $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: $.extend({ action: action }, data || {})
        }).done(function (result) {
            var response = parseResponse(result);

            if ((response.status || "") === "success" || (response.status || "") === "completed") {
                if (target) {
                    renderMessage(target, "success", response.message || "Saved successfully.");
                }
                if (options.onSuccess) {
                    options.onSuccess(response);
                }
            } else {
                if (target) {
                    renderMessage(target, "error", response.message || "Something went wrong.");
                }
                if (options.onError) {
                    options.onError(response);
                }
            }
        }).fail(function (xhr) {
            if (target) {
                renderMessage(target, "error", "Request failed. Please try again.");
            }

            if (options.onFail) {
                options.onFail(xhr);
            }
        }).always(function () {
            hideLoader();

            if (button && button.length && typeof WICore !== "undefined" && typeof WICore.removeLoadingButton === "function") {
                WICore.removeLoadingButton(button);
            }
        });
    }

    return {
        newItem: function (e) {
            preventEvent(e);
            openModal("menu-new");
        },

        editMenu: function (id) {
            showLoader();

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "editMenu",
                    id: id
                }
            }).done(function (result) {
                var response = parseResponse(result);
                var menu = normaliseMenuPayload(response);

                if ((response.status || "") === "success" || (response.status || "") === "completed") {
                    $("#edit_menu_id").val(menu.id || "");
                    $("#edit_menu_name").val(menu.name || "");
                    $("#edit_menu_link").val(menu.link || "");
                    openModal("menu-edit");
                } else {
                    renderMessage("#mresults", "error", response.message || "Unable to load menu item.");
                }
            }).fail(function () {
                renderMessage("#mresults", "error", "Unable to load menu item.");
            }).always(function () {
                hideLoader();
            });
        },

        menuEdit: function () {
            var payload = {
                menu: {
                    MenuData: {
                        id: $("#edit_menu_id").val(),
                        name: $("#edit_menu_name").val(),
                        link: $("#edit_menu_link").val()
                    },
                    FieldId: {
                        id: "id",
                        name: "name",
                        link: "link"
                    }
                }
            };

            post("menuEdit", payload, {
                target: "#mresults",
                loadingLabel: "Saving",
                onSuccess: function () {
                    closeModal("menu-edit");
                    refreshPage();
                }
            });
        },

        menunew: function () {
            var payload = {
                menu: {
                    MenuData: {
                        name: $("#new_menu_name").val(),
                        link: $("#new_menu_link").val()
                    },
                    FieldId: {
                        name: "name",
                        link: "link"
                    }
                }
            };

            post("newmenuitem", payload, {
                target: "#mresults",
                loadingLabel: "Adding",
                onSuccess: function () {
                    closeModal("menu-new");
                    refreshPage();
                }
            });
        },

        deleteMEnu: function () {
            var id = $(".delete_id").attr("id") || "";

            if (!id) {
                renderMessage("#mresults", "error", "No menu item selected.");
                return;
            }

            post("DeleteMenu", { id: id }, {
                target: "#mresults",
                loadingLabel: "Deleting",
                onSuccess: function () {
                    closeModal("menu-delete");
                    refreshPage();
                }
            });
        },

        closed: function (ele) {
            closeModal(ele);
        },

        deleteItem: function (id) {
            $(".delete_id").attr("id", id);
            openModal("menu-delete");
        },

        opemMenuCreate: function (e) {
            preventEvent(e);
            openModal("menu");
        },

        menuLink: function () {
            var name = $("#new_menu_name").val();
            var link = $("#new_menu_link").val();

            post("menuLink", {
                name: name,
                link: link
            }, {
                target: "#sbmresults",
                loadingLabel: "Creating",
                onSuccess: function () {
                    closeModal("menu");
                    refreshPage();
                }
            });
        }

         saveSidebarMenu: function () {
            var form = $("#wi-sidebar-menu-form");

            if (!form.length) {
                renderMessage("#sbmresults", "error", "Sidebar form not found.");
                return;
            }

            var button = $("#save_sidebar_menu");

            clearMessages();
            showLoader();

            if (button.length && typeof WICore !== "undefined" && typeof WICore.loadingButton === "function") {
                WICore.loadingButton(button, "Saving");
            }

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: form.serialize() + "&action=saveSidebarMenu"
            }).done(function (result) {
                var response = parseResponse(result);

                if ((response.status || "") === "success" || (response.status || "") === "completed") {
                    renderMessage("#sbmresults", "success", response.message || "Sidebar saved successfully.");
                    refreshPage();
                } else {
                    renderMessage("#sbmresults", "error", response.message || "Unable to save sidebar.");
                }
            }).fail(function () {
                renderMessage("#sbmresults", "error", "Request failed. Please try again.");
            }).always(function () {
                hideLoader();

                if (button.length && typeof WICore !== "undefined" && typeof WICore.removeLoadingButton === "function") {
                    WICore.removeLoadingButton(button);
                }
            });
        },


        newAdminMenuItem: function (e) {
            preventEvent(e);
            openModal("admin-menu-new");
        },

        editAdminMenu: function (id) {
            showLoader();

            $.ajax({
                url: ajaxUrl,
                type: "POST",
                data: {
                    action: "editAdminMenu",
                    id: id
                }
            }).done(function (result) {
                var response = parseResponse(result);
                var menu = normaliseMenuPayload(response);

                if ((response.status || "") === "success" || (response.status || "") === "completed") {
                    $("#edit_admin_menu_id").val(menu.id || "");
                    $("#edit_admin_menu_name").val(menu.label || menu.name || "");
                    $("#edit_admin_menu_link").val(menu.link || "");
                    $("#edit_admin_menu_lang").val(menu.lang || "");
                    openModal("admin-menu-edit");
                } else {
                    renderMessage("#admresults", "error", response.message || "Unable to load admin menu item.");
                }
            }).fail(function () {
                renderMessage("#admresults", "error", "Unable to load admin menu item.");
            }).always(function () {
                hideLoader();
            });
        },

        adminMenuEdit: function () {
            var payload = {
                menu: {
                    MenuData: {
                        id: $("#edit_admin_menu_id").val(),
                        name: $("#edit_admin_menu_name").val(),
                        label: $("#edit_admin_menu_name").val(),
                        link: $("#edit_admin_menu_link").val(),
                        lang: $("#edit_admin_menu_lang").val()
                    }
                }
            };

            post("adminMenuEdit", payload, {
                target: "#admresults",
                loadingLabel: "Saving",
                onSuccess: function () {
                    closeModal("admin-menu-edit");
                    refreshPage();
                }
            });
        },

        adminMenuNew: function () {
            var payload = {
                menu: {
                    MenuData: {
                        name: $("#new_admin_menu_name").val(),
                        label: $("#new_admin_menu_name").val(),
                        link: $("#new_admin_menu_link").val(),
                        lang: $("#new_admin_menu_lang").val(),
                        sort: $("#new_admin_menu_sort").val()
                    }
                }
            };

            post("newAdminMenuItem", payload, {
                target: "#admresults",
                loadingLabel: "Adding",
                onSuccess: function () {
                    closeModal("admin-menu-new");
                    refreshPage();
                }
            });
        },

        deleteAdminMenu: function (id) {
            $(".delete_admin_menu_id").attr("id", id);
            openModal("admin-menu-delete");
        },

        deleteAdminMenuConfirm: function () {
            var id = $(".delete_admin_menu_id").attr("id") || "";

            if (!id) {
                renderMessage("#admresults", "error", "No admin menu item selected.");
                return;
            }

            post("deleteAdminMenu", { id: id }, {
                target: "#admresults",
                loadingLabel: "Deleting",
                onSuccess: function () {
                    closeModal("admin-menu-delete");
                    refreshPage();
                }
            });
        },



    };
})(jQuery);

$(document).ready(function () {
    // Reserved for future menu-specific bindings.
});