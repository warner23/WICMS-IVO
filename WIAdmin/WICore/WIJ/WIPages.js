var WIPages = {};

WIPages.openCreateModal = function () {
    $("#wi-page-modal-title").text("Add Page");
    $("#page-id").val(0);
    $("#page-name").val("");
    $("#page-contents").val("");
    $("#page-panel").val("0");
    $("#page-top-head").val("0");
    $("#page-header").val("0");
    $("#page-left-sidebar").val("0");
    $("#page-right-sidebar").val("0");
    $("#page-footer").val("0");
    $("#page-results").html("");
    $("#wi-page-modal").removeClass("hide").addClass("show");
};

WIPages.openEditModal = function (page) {
    $("#wi-page-modal-title").text("Edit Page");
    $("#page-id").val(page.id || 0);
    $("#page-name").val(page.name || "");
    $("#page-contents").val(page.contents || "");
    $("#page-panel").val(String(page.panel || "0"));
    $("#page-top-head").val(String(page.top_head || "0"));
    $("#page-header").val(String(page.header || "0"));
    $("#page-left-sidebar").val(String(page.left_sidebar || "0"));
    $("#page-right-sidebar").val(String(page.right_sidebar || "0"));
    $("#page-footer").val(String(page.footer || "0"));
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
            footer: $("#page-footer").val()
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

                $("#page-results").html('<div class="alert alert-success">' + res.msg + '</div>');
                window.location.reload();
            } catch (e) {
                $("#page-results").html('<div class="alert alert-danger">Unexpected server response.</div>');
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