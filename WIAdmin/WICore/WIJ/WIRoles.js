var WIRoles = {};

WIRoles.openCreateModal = function () {
    $("#wi-role-modal-title").text("Add Role");
    $("#role-id").val(0);
    $("#role-name").val("");
    $("#role-results").html("");
    $("#wi-role-modal").removeClass("hide").addClass("show");
};

WIRoles.openEditModal = function (role) {
    $("#wi-role-modal-title").text("Edit Role");
    $("#role-id").val(role.role_id || 0);
    $("#role-name").val(role.role || "");
    $("#role-results").html("");
    $("#wi-role-modal").removeClass("hide").addClass("show");
};

WIRoles.closeModal = function () {
    $("#wi-role-modal").removeClass("show").addClass("hide");
};

WIRoles.saveRole = function () {
    WICore.removeErrorMessages();

    var btn = $("#role-save-btn");

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "saveRole",
            role_id: $("#role-id").val(),
            role: $("#role-name").val()
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
                        $("#role-results").html('<div class="alert alert-danger">' + res.msg + '</div>');
                    }
                    return;
                }

                $("#role-results").html('<div class="alert alert-success">' + res.msg + '</div>');
                window.location.reload();
            } catch (e) {
                $("#role-results").html('<div class="alert alert-danger">Unexpected server response.</div>');
            }
        }
    });
};

WIRoles.deleteRole = function (roleId) {
    if (!confirm("Are you sure you want to delete this role?")) {
        return;
    }

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "deleteRole",
            roleId: roleId
        },
        success: function (result) {
            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status === "success") {
                    $("#role-row-" + roleId).fadeOut("slow", function () {
                        $(this).remove();
                    });
                } else {
                    alert(res.msg || "Unable to delete role.");
                }
            } catch (e) {
                alert("Unexpected server response.");
            }
        }
    });
};

WIRoles.openPermissionsModal = function (roleId, roleName) {
    $("#role-permissions-role-id").val(roleId);
    $("#wi-role-permissions-title").text("Role Permissions - " + roleName);
    $("#role-permissions-results").html("");
    $(".role-permission-checkbox").prop("checked", false);

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "getRolePermissions",
            role_id: roleId
        },
        success: function (result) {
            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status === "success" && Array.isArray(res.data)) {
                    for (var i = 0; i < res.data.length; i++) {
                        $('.role-permission-checkbox[value="' + res.data[i] + '"]').prop("checked", true);
                    }
                }

                $("#wi-role-permissions-modal").removeClass("hide").addClass("show");
            } catch (e) {
                alert("Failed to load role permissions.");
            }
        }
    });
};

WIRoles.closePermissionsModal = function () {
    $("#wi-role-permissions-modal").removeClass("show").addClass("hide");
};

WIRoles.saveRolePermissions = function () {
    var btn = $("#role-permissions-save-btn");
    var roleId = $("#role-permissions-role-id").val();
    var permissionIds = [];

    $(".role-permission-checkbox:checked").each(function () {
        permissionIds.push($(this).val());
    });

    $.ajax({
        url: "WICore/WIClass/WIAjax.php",
        type: "POST",
        data: {
            action: "updateRolePermissions",
            role_id: roleId,
            permission_ids: permissionIds
        },
        beforeSend: function () {
            WICore.loadingButton(btn, "Saving...");
        },
        success: function (result) {
            WICore.removeLoadingButton(btn);

            try {
                var res = (typeof result === "object") ? result : JSON.parse(result);

                if (res.status === "success") {
                    $("#role-permissions-results").html('<div class="alert alert-success">' + res.msg + '</div>');
                    window.location.reload();
                } else {
                    $("#role-permissions-results").html('<div class="alert alert-danger">' + (res.msg || "Unable to save permissions.") + '</div>');
                }
            } catch (e) {
                $("#role-permissions-results").html('<div class="alert alert-danger">Unexpected server response.</div>');
            }
        }
    });
};

$(document).ready(function () {
    $(document).on("click", "#wi-role-modal", function (e) {
        if ($(e.target).is("#wi-role-modal")) {
            WIRoles.closeModal();
        }
    });

    $(document).on("click", "#wi-role-permissions-modal", function (e) {
        if ($(e.target).is("#wi-role-permissions-modal")) {
            WIRoles.closePermissionsModal();
        }
    });
});