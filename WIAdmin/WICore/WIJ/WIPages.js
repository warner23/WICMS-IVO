var WIPages = {};

WIPages.openCreateModal = function () {
    $("#wi-page-modal-title").text("Add Page");
    $("#page-id").val(0);
    $("#page-name").prop("readonly", false).val("");
    $("#page-contents").val("notfound");
    $("#page-panel").val("1");
    $("#page-top-head").val("0");
    $("#page-header").val("0");
    $("#page-left-sidebar").val("0");
    $("#page-right-sidebar").val("0");
    $("#page-footer").val("1");
    $("#page-create-route").prop("checked", true);
    $("#page-create-defaults").prop("checked", true);
    $("#page-overwrite-route").prop("checked", false);
    $("#page-results").html("");
    $("#wi-page-modal").removeClass("hide").addClass("show");
};

WIPages.openEditModal = function (page) {
    $("#wi-page-modal-title").text("Edit / Assign Page Module");
    $("#page-id").val(page.id || 0);
    $("#page-name").prop("readonly", false).val(page.name || "");
    $("#page-contents").val(page.contents || "notfound");
    $("#page-panel").val(String(page.panel || "0"));
    $("#page-top-head").val(String(page.top_head || "0"));
    $("#page-header").val(String(page.header || "0"));
    $("#page-left-sidebar").val(String(page.left_sidebar || "0"));
    $("#page-right-sidebar").val(String(page.right_sidebar || "0"));
    $("#page-footer").val(String(page.footer || "0"));
    $("#page-create-route").prop("checked", false);
    $("#page-create-defaults").prop("checked", false);
    $("#page-overwrite-route").prop("checked", false);
    $("#page-results").html("");
    $("#wi-page-modal").removeClass("hide").addClass("show");
};

WIPages.closeModal = function () {
    $("#wi-page-modal").removeClass("show").addClass("hide");
};

WIPages.savePage = function () {
    WICore.removeErrorMessages();

    var btn = $("#page-save-btn");

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "savePage",
            id: $("#page-id").val(),
            name: $("#page-name").val(),
            contents: $("#page-contents").val(),
            panel: $("#page-panel").val(),
            top_head: $("#page-top-head").val(),
            header: $("#page-header").val(),
            left_sidebar: $("#page-left-sidebar").val(),
            right_sidebar: $("#page-right-sidebar").val(),
            footer: $("#page-footer").val(),
            create_route: $("#page-create-route").is(":checked") ? "1" : "0",
            create_defaults: $("#page-create-defaults").is(":checked") ? "1" : "0",
            overwrite_route: $("#page-overwrite-route").is(":checked") ? "1" : "0"
        },
        beforeSend: function () {
            WICore.loadingButton(btn, "Saving...");
        },
        success: function (result) {
            WICore.removeLoadingButton(btn);

            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status === "error") {
                    if (res.errors) {
                        for (var i = 0; i < res.errors.length; i++) {
                            var error = res.errors[i];
                            WICore.displayadminerrorsMessage($("#" + error.id), error.msg);
                        }
                    } else if (res.msg) {
                        $("#page-results").html('<div class="alert alert-danger">' + res.msg + '</div>');
                    }
                    return;
                }

                var detail = "";

                if (res.created && res.created.length) {
                    detail += '<p class="small text-muted">Created/verified: ' + res.created.join(", ") + '</p>';
                }

                if (res.warnings && res.warnings.length) {
                    detail += '<p class="small text-warning">' + res.warnings.join("<br>") + '</p>';
                }

                $("#page-results").html('<div class="alert alert-success">' + res.msg + detail + '</div>');
                window.location.reload();
            } catch (e) {
                $("#page-results").html('<div class="alert alert-danger">Unexpected server response.</div>');
            }
        }
    });
};

WIPages.assignModule = function (id, pageName, currentModule) {
    var moduleName = prompt("Assign contents module for " + pageName + ":", currentModule || "notfound");

    if (moduleName === null) {
        return;
    }

    moduleName = String(moduleName).replace(/\s+/g, "_").replace(/[^a-zA-Z0-9_\-]/g, "");

    if (moduleName === "") {
        alert("Module name is required.");
        return;
    }

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "assignPageModule",
            id: id,
            page: pageName,
            contents: moduleName
        },
        success: function (result) {
            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status !== "success") {
                    alert(res.msg || "Unable to assign module.");
                    return;
                }

                window.location.reload();
            } catch (e) {
                alert("Unexpected server response.");
            }
        }
    });
};

WIPages.deletePage = function (id) {
    if (!confirm("Are you sure you want to delete this page?")) {
        return;
    }

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "deletePage",
            id: id
        },
        success: function (result) {
            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status === "success") {
                    $("#page-row-" + id).fadeOut("slow", function () {
                        $(this).remove();
                    });
                } else {
                    alert(res.msg || "Unable to delete page.");
                }
            } catch (e) {
                alert("Unexpected server response.");
            }
        }
    });
};

$(document).ready(function () {
    $(document).on("click", "#wi-page-modal", function (e) {
        if ($(e.target).is("#wi-page-modal")) {
            WIPages.closeModal();
        }
    });
});
