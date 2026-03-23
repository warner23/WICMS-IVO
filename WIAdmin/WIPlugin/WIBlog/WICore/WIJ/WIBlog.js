/**
 * WIBlog Javascript Controller
 * Location: WIPlugin/WIBlog/WICore/WIJ/WIBlog.js
 */

var WIBlog = {

    ajaxUrl: "WICore/WIClass/WIAjax.php",

    init: function () {
        WIBlog.bindOptionSave();
        WIBlog.bindOptionFeedbackDismiss();
    },

    bindOptionSave: function () {
        $(document).on("submit", "#wiblog-options-form", function (e) {
            e.preventDefault();
            WIBlog.saveOptions($(this));
        });
    },

    bindOptionFeedbackDismiss: function () {
        $(document).on("click", ".wiblog-alert .close", function () {
            $(this).closest(".wiblog-alert").fadeOut(200, function () {
                $(this).remove();
            });
        });
    },

    saveOptions: function ($form) {
        var data = WIBlog.serializeForm($form);

        data.action = "wiblog_save_options";

        WIBlog.setSavingState(true);

        $.ajax({
            url: WIBlog.ajaxUrl,
            type: "POST",
            dataType: "json",
            data: data,
            success: function (response) {
                WIBlog.setSavingState(false);

                if (response && response.status === "success") {
                    WIBlog.showMessage("success", response.message || "Blog options saved successfully.");
                    return;
                }

                WIBlog.showMessage("error", (response && response.message) ? response.message : "Failed to save blog options.");
            },
            error: function (xhr) {
                WIBlog.setSavingState(false);

                console.log("WIBlog AJAX error:", xhr.responseText);

                WIBlog.showMessage(
                    "error",
                    "An unexpected error occurred while saving WIBlog options."
                );
            }
        });
    },

    serializeForm: function ($form) {
        var rawArray = $form.serializeArray();
        var data = {};

        $.each(rawArray, function (_, field) {
            data[field.name] = field.value;
        });

        // Make sure unchecked checkboxes still get posted as 0
        $form.find('input[type="checkbox"]').each(function () {
            var name = $(this).attr("name");

            if (!name) {
                return;
            }

            data[name] = $(this).is(":checked") ? "1" : "0";
        });

        return data;
    },

    setSavingState: function (isSaving) {
        var $form = $("#wiblog-options-form");
        var $submit = $form.find('button[type="submit"]');

        if (!$submit.length) {
            return;
        }

        if (isSaving) {
            $submit.data("original-text", $submit.text());
            $submit.prop("disabled", true).text("Saving...");
            return;
        }

        var original = $submit.data("original-text") || "Save Options";
        $submit.prop("disabled", false).text(original);
    },

    showMessage: function (type, message) {
        var cls = type === "success" ? "alert-success" : "alert-danger";

        $(".wiblog-alert").remove();

        var html =
            '<div class="alert ' + cls + ' alert-dismissible wiblog-alert" role="alert" style="margin-bottom:18px;border-radius:10px;">' +
                '<button type="button" class="close" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                message +
            '</div>';

        $("#wiblog-options-form").before(html);
    }
};

$(document).ready(function () {
    WIBlog.init();
});