"use strict";

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WIKitchenCompli / WICOS
| Project: WI Ecosystem
| File: /root/WIAdmin/WICore/WIJ/WIModalFlow.js
| Type: Shared UI Helper
| Layer: Admin UI
| Purpose Area: Shared Modal Internal Flow
| Version: 1.0.0
| Created: 2026-04-23
| Last Updated: 2026-04-23
| Status: Production Ready
|--------------------------------------------------------------------------
*/

window.WIModalFlow = (function ($, window, document) {
    var selectors = {
        proofModal: "#modal-checklist-proof-details",
        chooser: "#wiProofSourceChooser",
        camera: "#wiProofPanelCamera",
        upload: "#wiProofPanelUpload",
        library: "#wiProofPanelLibrary",
        sourceButtons: ".wiProofSourceBtn",
        backButtons: ".wiProofBackBtn"
    };

    var currentPayload = {};

    function resetPanels() {
        $(selectors.camera).hide();
        $(selectors.upload).hide();
        $(selectors.library).hide();
        $(selectors.chooser).show();
    }

    function openPanel(target) {
        resetPanels();

        if (target === "camera") {
            $(selectors.chooser).hide();
            $(selectors.camera).show();
        } else if (target === "upload") {
            $(selectors.chooser).hide();
            $(selectors.upload).show();
        } else if (target === "library") {
            $(selectors.chooser).hide();
            $(selectors.library).show();
        }
    }

    function bindEvents() {
        $(document)
            .off("click.wiProofSourceBtn", selectors.sourceButtons)
            .on("click.wiProofSourceBtn", selectors.sourceButtons, function () {
                openPanel(String($(this).attr("data-target-panel") || ""));
            });

        $(document)
            .off("click.wiProofBackBtn", selectors.backButtons)
            .on("click.wiProofBackBtn", selectors.backButtons, function () {
                resetPanels();
            });
    }

    function openChecklistProofFlow(payload) {
        currentPayload = payload || {};
        resetPanels();
        $(selectors.proofModal).modal("show");
    }

    function getCurrentPayload() {
        return currentPayload;
    }

    function init() {
        bindEvents();
    }

    return {
        init: init,
        openChecklistProofFlow: openChecklistProofFlow,
        getCurrentPayload: getCurrentPayload
    };
})(jQuery, window, document);

jQuery(function () {
    if (window.WIModalFlow && typeof window.WIModalFlow.init === "function") {
        window.WIModalFlow.init();
    }
});