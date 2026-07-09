/**
 * FILE:
 * WICMS-IVO/WIAdmin/WICore/WIJ/roles.js
 *
 * Canonical admin role management handler for WICMS.
 */

var roles = (function () {

    function addRole() {
        var roleField = $("#role-name");
        var role = $.trim(roleField.val());

        WICore.removeErrorMessages();

        if (role === "") {
            WICore.displayErrorMessage(roleField, $_lang.field_required || "Role name is required.");
            return false;
        }

        WICore.ajax({
            url: "WICore/WIClass/WIAjax.php",
            data: {
                action: "addRole",
                role: role,
                csrf_token: WICore.getCSRF()
            },

            onSuccess: function (response) {
                var roleName = response.roleName || role;
                var roleId = response.roleId || "";

                var html = ''
                    + '<tr class="role-row">'
                    + '<td>' + roleName + '</td>'
                    + '<td>0</td>'
                    + '<td>'
                    + '<button type="button" class="btn btn-danger btn-sm" onclick="roles.deleteRole(this,' + roleId + ');">'
                    + '<i class="icon-trash glyphicon glyphicon-trash"></i> ' + ($_lang.delete || 'Delete')
                    + '</button>'
                    + '</td>'
                    + '</tr>';

                $(".roles-table").append(html);
                roleField.val("");
            },

            onError: function (response) {
                WICore.displayErrorMessage(roleField, response.message || $_lang.error_updating_db || "Unable to add role.");
            }
        });

        return false;
    }

    function deleteRole(element, roleId) {
        var confirmed = confirm($_lang.are_you_sure || "Are you sure?");

        if (!confirmed) {
            return;
        }

        WICore.ajax({
            url: "WICore/WIClass/WIAjax.php",
            data: {
                action: "deleteRole",
                roleId: roleId,
                csrf_token: WICore.getCSRF()
            },

            onSuccess: function () {
                $(element).closest(".role-row").fadeOut(300, function () {
                    $(this).remove();
                });
            },

            onError: function (response) {
                alert(response.message || "Unable to delete role.");
            }
        });
    }

    return {
        addRole: addRole,
        deleteRole: deleteRole
    };

})();