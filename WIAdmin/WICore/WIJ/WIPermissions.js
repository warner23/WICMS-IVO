var WIPermissions = {};

WIPermissions.openCreateModal = function () {
    $("#wi-permission-modal-title").text("Add Permission");
    $("#permission-id").val(0);
    $("#permission-name").val("");
    $("#permission-code").val("");
    $("#permission-group").val("");
    $("#permission-description").val("");
    $("#permission-active").val("1");
    $("#permission-results").html("");
    $("#wi-permission-modal").removeClass("hide").addClass("show");
};

WIPermissions.openEditModal = function (permission) {
    $("#wi-permission-modal-title").text("Edit Permission");
    $("#permission-id").val(permission.id || 0);
    $("#permission-name").val(permission.name || "");
    $("#permission-code").val(permission.code || "");
    $("#permission-group").val(permission.group_name || "");
    $("#permission-description").val(permission.description || "");
    $("#permission-active").val(String(permission.is_active || 0));
    $("#permission-results").html("");
    $("#wi-permission-modal").removeClass("hide").addClass("show");
};

WIPermissions.closeModal = function () {
    $("#wi-permission-modal").removeClass("show").addClass("hide");
};

WIPermissions.savePermission = function () {
    WICore.removeErrorMessages();

    var btn = $("#permission-save-btn");

    var data = {
        action: "savePermission",
        id: $("#permission-id").val(),
        name: $("#permission-name").val(),
        code: $("#permission-code").val(),
        group_name: $("#permission-group").val(),
        description: $("#permission-description").val(),
        is_active: $("#permission-active").val()
    };

    WICore.loadingButton(btn, "Saving...");

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: data,
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
                        $("#permission-results").html('<div class="alert alert-danger">' + res.msg + '</div>');
                    }

                    return;
                }

                $("#permission-results").html('<div class="alert alert-success">' + res.msg + '</div>');
                window.location.reload();
            } catch (e) {
                $("#permission-results").html('<div class="alert alert-danger">Unexpected server response.</div>');
            }
        }
    });
};

WIPermissions.deletePermission = function (id) {
    var confirmDelete = confirm("Are you sure you want to delete this permission?");
    if (!confirmDelete) {
        return;
    }

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "deletePermission",
            id: id
        },
        success: function (result) {
            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status === "success") {
                    $("#permission-row-" + id).fadeOut("slow", function () {
                        $(this).remove();
                    });
                } else {
                    alert(res.msg || "Unable to delete permission.");
                }
            } catch (e) {
                alert("Unexpected server response.");
            }
        }
    });
};

$(document).ready(function () {
    $(document).on("change", ".wi-role-permission-toggle", function () {
        var checkbox = $(this);

        $.ajax({
            url: "WICore/WIClass/WIAjax.php",
            type: "POST",
            data: {
                action: "toggleRolePermission",
                role_id: checkbox.data("role-id"),
                permission_id: checkbox.data("permission-id"),
                enabled: checkbox.is(":checked") ? 1 : 0
            },
            error: function () {
                checkbox.prop("checked", !checkbox.is(":checked"));
                alert("Failed to update role permission.");
            },
            success: function (result) {
                try {
                    var res = (typeof result === "object") ? result : JSON.parse(result);

                    if (res.status !== "success") {
                        checkbox.prop("checked", !checkbox.is(":checked"));
                        alert(res.msg || "Failed to update role permission.");
                    }
                } catch (e) {
                    checkbox.prop("checked", !checkbox.is(":checked"));
                    alert("Unexpected server response.");
                }
            }
        });
    });

    $(document).on("click", "#wi-permission-modal", function (e) {
        if ($(e.target).is("#wi-permission-modal")) {
            WIPermissions.closeModal();
        }
    });
});