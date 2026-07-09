(function ($) {
    "use strict";

    /**
     * ---------------------------------------------------------------------
     * File Information
     * ---------------------------------------------------------------------
     * Written By: Jules Warner
     * Company: WILabs
     * Product: WI Builder
     * Project: WI Ecosystem
     * File: WIDragDrop.js
     * Location: /WIAdmin/WICore/WIJ/WIDragDrop.js
     * Type: Shared Admin JavaScript Controller
     * Layer: Admin
     * Purpose Area: Shared Builder Drag / Drop Logic
     * Version: 1.0.0
     * Created: 2026-04-25
     * Last Updated: 2026-04-25
     * Status: Active
     * ---------------------------------------------------------------------
     * Summary
     * ---------------------------------------------------------------------
     * Shared drag/drop helper for admin builder screens.
     *
     * Responsibilities:
     * - Wire draggable items
     * - Wire sortable/drop zones
     * - Trigger callbacks when layout changes
     * - Stay reusable outside compliance-specific pages
     * ---------------------------------------------------------------------
     */

    var WIDragDrop = {
        /**
         * Default options.
         */
        defaults: {
            draggableSelector: "[data-builder-draggable]",
            sortableSelector: "[data-builder-sortable]",
            handleSelector: "[data-builder-drag-handle]",
            onUpdate: null,
            onReceive: null,
            onStart: null,
            onStop: null
        },

        /**
         * Runtime options.
         */
        options: {},

        /**
         * Initialises the drag/drop helper.
         *
         * @param {Object} options Runtime options.
         * @returns {Object}
         */
        init: function (options) {
            this.options = $.extend({}, this.defaults, options || {});
            this.initSortables();
            this.initDraggables();

            return this;
        },

        /**
         * Initialises sortable/drop areas.
         *
         * @returns {void}
         */
        initSortables: function () {
            var self = this;

            if (!$.isFunction($.fn.sortable)) {
                return;
            }

            $(this.options.sortableSelector).each(function () {
                var $sortable = $(this);

                if ($sortable.data("wi-sortable-init") === 1) {
                    return;
                }

                $sortable.sortable({
                    connectWith: self.options.sortableSelector,
                    placeholder: "wi-builder-sort-placeholder",
                    handle: self.options.handleSelector,
                    tolerance: "pointer",
                    items: "> [data-builder-block]",
                    start: function (event, ui) {
                        if ($.isFunction(self.options.onStart)) {
                            self.options.onStart(event, ui, $sortable);
                        }
                    },
                    stop: function (event, ui) {
                        if ($.isFunction(self.options.onStop)) {
                            self.options.onStop(event, ui, $sortable);
                        }

                        if ($.isFunction(self.options.onUpdate)) {
                            self.options.onUpdate(event, ui, $sortable);
                        }
                    },
                    receive: function (event, ui) {
                        if ($.isFunction(self.options.onReceive)) {
                            self.options.onReceive(event, ui, $sortable);
                        }

                        if ($.isFunction(self.options.onUpdate)) {
                            self.options.onUpdate(event, ui, $sortable);
                        }
                    },
                    update: function (event, ui) {
                        if ($.isFunction(self.options.onUpdate)) {
                            self.options.onUpdate(event, ui, $sortable);
                        }
                    }
                });

                $sortable.data("wi-sortable-init", 1);
            });
        },

        /**
         * Initialises external draggable items.
         *
         * @returns {void}
         */
        initDraggables: function () {
            if (!$.isFunction($.fn.draggable)) {
                return;
            }

            $(this.options.draggableSelector).each(function () {
                var $item = $(this);

                if ($item.data("wi-draggable-init") === 1) {
                    return;
                }

                $item.draggable({
                    helper: "clone",
                    appendTo: "body",
                    zIndex: 9999,
                    revert: "invalid",
                    handle: $item.find("[data-builder-drag-handle]").length
                        ? "[data-builder-drag-handle]"
                        : false
                });

                $item.data("wi-draggable-init", 1);
            });
        },

        /**
         * Destroys current sortable/draggable bindings.
         *
         * @returns {void}
         */
        destroy: function () {
            if ($.isFunction($.fn.sortable)) {
                $(this.options.sortableSelector).each(function () {
                    var $sortable = $(this);

                    if ($sortable.data("wi-sortable-init") === 1) {
                        $sortable.sortable("destroy");
                        $sortable.removeData("wi-sortable-init");
                    }
                });
            }

            if ($.isFunction($.fn.draggable)) {
                $(this.options.draggableSelector).each(function () {
                    var $item = $(this);

                    if ($item.data("wi-draggable-init") === 1) {
                        $item.draggable("destroy");
                        $item.removeData("wi-draggable-init");
                    }
                });
            }
        },

        /**
         * Returns block IDs from a sortable container.
         *
         * @param {jQuery} $container Sortable container.
         * @returns {Array}
         */
        getBlockIdsFromContainer: function ($container) {
            var ids = [];

            $container.find("> [data-builder-block]").each(function () {
                var blockId = String($(this).attr("data-block-id") || "");

                if (blockId !== "") {
                    ids.push(blockId);
                }
            });

            return ids;
        },

        /**
         * Returns section payload from a sortable section node.
         *
         * @param {jQuery} $section Section node.
         * @returns {Object}
         */
        getSectionPayload: function ($section) {
            return {
                section_id: String($section.attr("data-section-id") || ""),
                section_code: String($section.attr("data-section-code") || ""),
                section_title: String($section.attr("data-section-title") || ""),
                question_ids: this.getQuestionIdsFromSection($section)
            };
        },

        /**
         * Returns question IDs from a section.
         *
         * @param {jQuery} $section Section node.
         * @returns {Array}
         */
        getQuestionIdsFromSection: function ($section) {
            var ids = [];

            $section.find("[data-builder-sortable] > [data-builder-block]").each(function () {
                var questionId = parseInt($(this).attr("data-source-id") || 0, 10) || 0;

                if (questionId > 0) {
                    ids.push(questionId);
                }
            });

            return ids;
        }
    };

    window.WIDragDrop = WIDragDrop;
})(jQuery);