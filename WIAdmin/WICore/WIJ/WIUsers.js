/**
 * FILE:
 * WICMS-IVO/WIAdmin/WICore/WIJ/WIUsers.js
 *
 * Canonical admin user management handler for WICMS.
 */

var users = (function () {

    function displayInfo(userId) {
        var username = $("#modal-username");
        var email = $("#modal-email");
        var firstName = $("#modal-first-name");
        var lastName = $("#modal-last-name");
        var address = $("#modal-address");
        var phone = $("#modal-phone");
        var lastLogin = $("#modal-last-login");
        var ajaxLoading = $("#ajax-loading");
        var detailsBody = $("#details-body");
        var modal = $("#modal-user-details");

        modal.modal("show");
        username.text($_lang.loading || "Loading...");
        ajaxLoading.show();
        detailsBody.hide();

        WICore.ajax({
            url: "WICore/WIClass/WIAjax.php",
            data: {
                action: "getUserDetails",
                userId: userId,
                csrf_token: WICore.getCSRF()
            },

            onSuccess: function (res) {
                username.text(res.username || "");
                email.text(res.email || "");
                firstName.text(res.first_name || "");
                lastName.text(res.last_name || "");
                address.text(res.address || "");
                phone.text(res.phone || "");
                lastLogin.text(res.last_login || "");

                ajaxLoading.hide();
                detailsBody.show();
            },

            onError: function (response) {
                ajaxLoading.hide();
                detailsBody.show();
                username.text(response.message || "Unable to load user.");
            }
        });
    }

    function deleteUser(element, userId) {
        var userRow = $(element).closest(".user-row");
        var confirmed = confirm($_lang.are_you_sure || "Are you sure?");

        if (!confirmed) {
            return;
        }

        WICore.ajax({
            url: "WICore/WIClass/WIAjax.php",
            data: {
                action: "deleteUser",
                userId: userId,
                csrf_token: WICore.getCSRF()
            },

            onSuccess: function () {
                userRow.fadeOut(300, function () {
                    $(this).remove();
                });
            },

            onError: function (response) {
                alert(response.message || "Unable to delete user.");
            }
        });
    }

    function changeRole(element, role, userId) {
        WICore.ajax({
            url: "WICore/WIClass/WIAjax.php",
            data: {
                action: "changeRole",
                userId: userId,
                role: role,
                csrf_token: WICore.getCSRF()
            },

            onSuccess: function (response) {
                element.text(response.role || role);
            },

            onError: function (response) {
                alert(response.message || "Unable to update role.");
            }
        });
    }

    function roleChanger(element, userId) {
        $("#modal-change-role").modal({
            keyboard: false,
            backdrop: "static",
            show: true
        });

        var userRoleSpan = $(element).closest(".btn-group").find(".user-role");

        $("#change-role-button").off("click").on("click", function () {
            var newRole = $("#select-user-role").val();
            changeRole(userRoleSpan, newRole, userId);
        });
    }

    function showAddUserModal() {
        $("#modal-add-edit-user").modal({
            keyboard: false,
            backdrop: "static",
            show: true
        });
    }

    function resetAddEditForm() {
        $("#modal-add-edit-user").find("input, textarea").val("");
        WICore.removeErrorMessages();
    }

    return {
        displayInfo: displayInfo,
        deleteUser: deleteUser,
        changeRole: changeRole,
        roleChanger: roleChanger,
        showAddUserModal: showAddUserModal,
        resetAddEditForm: resetAddEditForm
    };

})();