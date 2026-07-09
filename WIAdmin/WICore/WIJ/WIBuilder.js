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
     * File: WIBuilder.js
     * Location: /WIAdmin/WICore/WIJ/WIBuilder.js
     * Type: Shared Admin JavaScript Controller
     * Layer: Admin
     * Purpose Area: Shared Builder State / AJAX / Rendering Helpers
     * Version: 1.0.0
     * Created: 2026-04-25
     * Last Updated: 2026-04-25
     * Status: Active
     * ---------------------------------------------------------------------
     * Summary
     * ---------------------------------------------------------------------
     * Shared builder controller used by admin builder pages.
     *
     * Responsibilities:
     * - Manage shared builder state
     * - Provide AJAX helper methods
     * - Provide common HTML escaping and helper methods
     * - Remain generic so compliance and non-compliance builders can reuse it
     * ---------------------------------------------------------------------
     */

    var WIBuilder = {
        /**
         * Shared runtime state.
         */
        state: {
            builderType: "",
            sourceId: 0,
            layout: {
                layout_version: "1.0.0",
                builder_type: "generic",
                meta: {},
                sections: []
            },
            rawData: {},
            isDirty: false,
            lastSavedAt: "",
            debug: false
        },

        /**
         * Default shared selectors.
         */
        selectors: {
            root: "[data-wi-builder]",
            canvas: "[data-builder-canvas]",
            summary: "[data-builder-summary]",
            saveButton: "[data-builder-save]",
            previewButton: "[data-builder-preview]",
            messageTarget: "[data-builder-message]"
        },

        /**
         * Initialises the shared builder.
         *
         * @param {Object} options Optional configuration.
         * @returns {Object}
         */
        init: function (options) {
            options = options || {};

            this.state.builderType = String(options.builderType || this.state.builderType || "generic");
            this.state.sourceId = parseInt(options.sourceId || this.state.sourceId || 0, 10) || 0;
            this.state.debug = !!options.debug;

            if ($.isPlainObject(options.layout)) {
                this.state.layout = this.normaliseLayout(options.layout);
            }

            if ($.isPlainObject(options.rawData)) {
                this.state.rawData = options.rawData;
            }

            return this;
        },

        /**
         * Returns whether the builder root exists.
         *
         * @returns {boolean}
         */
        exists: function () {
            return $(this.selectors.root).length > 0;
        },

        /**
         * Marks the builder dirty.
         *
         * @returns {void}
         */
        markDirty: function () {
            this.state.isDirty = true;
            this.renderSummary();
        },

        /**
         * Marks the builder clean.
         *
         * @returns {void}
         */
        markClean: function () {
            this.state.isDirty = false;
            this.state.lastSavedAt = this.getCurrentDateTimeString();
            this.renderSummary();
        },

        /**
         * Returns the current layout.
         *
         * @returns {Object}
         */
        getLayout: function () {
            return this.state.layout || {
                layout_version: "1.0.0",
                builder_type: this.state.builderType || "generic",
                meta: {},
                sections: []
            };
        },

        /**
         * Replaces the current layout.
         *
         * @param {Object} layout Layout payload.
         * @returns {void}
         */
        setLayout: function (layout) {
            this.state.layout = this.normaliseLayout(layout || {});
            this.renderSummary();
        },

        /**
         * Returns the sections array from the current layout.
         *
         * @returns {Array}
         */
        getSections: function () {
            var layout = this.getLayout();
            return $.isArray(layout.sections) ? layout.sections : [];
        },

        /**
         * Replaces the section array on the current layout.
         *
         * @param {Array} sections Section rows.
         * @returns {void}
         */
        setSections: function (sections) {
            var layout = this.getLayout();
            layout.sections = $.isArray(sections) ? sections : [];
            this.state.layout = this.normaliseLayout(layout);
            this.markDirty();
        },

        /**
         * Returns one section by section ID.
         *
         * @param {string} sectionId Section ID.
         * @returns {Object|null}
         */
        getSectionById: function (sectionId) {
            var targetId = String(sectionId || "");

            if (targetId === "") {
                return null;
            }

            var sections = this.getSections();
            var index;

            for (index = 0; index < sections.length; index += 1) {
                if (String(sections[index].section_id || "") === targetId) {
                    return sections[index];
                }
            }

            return null;
        },

        /**
         * Adds a new section to the layout.
         *
         * @param {Object} section Section payload.
         * @returns {Object}
         */
        addSection: function (section) {
            var sections = this.getSections();
            var sortOrder = this.getNextSectionSortOrder(sections);

            sections.push({
                section_id: String(section.section_id || ("section_" + sortOrder)),
                section_code: String(section.section_code || ("section_" + sortOrder)),
                section_title: String(section.section_title || "Section"),
                section_description: String(section.section_description || ""),
                sort_order: parseInt(section.sort_order || sortOrder, 10) || sortOrder,
                blocks: $.isArray(section.blocks) ? section.blocks : []
            });

            this.setSections(sections);

            return this.getLayout();
        },

        /**
         * Updates one section.
         *
         * @param {string} sectionId Section ID.
         * @param {Object} updates Section updates.
         * @returns {Object}
         */
        updateSection: function (sectionId, updates) {
            var sections = this.getSections();
            var targetId = String(sectionId || "");
            var index;

            updates = updates || {};

            for (index = 0; index < sections.length; index += 1) {
                if (String(sections[index].section_id || "") !== targetId) {
                    continue;
                }

                sections[index] = $.extend(true, {}, sections[index], updates);
                break;
            }

            this.setSections(sections);

            return this.getLayout();
        },

        /**
         * Removes one section.
         *
         * @param {string} sectionId Section ID.
         * @returns {Object}
         */
        removeSection: function (sectionId) {
            var targetId = String(sectionId || "");
            var sections = $.grep(this.getSections(), function (section) {
                return String(section.section_id || "") !== targetId;
            });

            this.setSections(sections);

            return this.getLayout();
        },

        /**
         * Adds a block to a section.
         *
         * @param {string} sectionId Section ID.
         * @param {Object} block Block payload.
         * @returns {Object}
         */
        addBlockToSection: function (sectionId, block) {
            var sections = this.getSections();
            var targetId = String(sectionId || "");
            var index;
            var nextSortOrder;

            block = block || {};

            for (index = 0; index < sections.length; index += 1) {
                if (String(sections[index].section_id || "") !== targetId) {
                    continue;
                }

                if (!$.isArray(sections[index].blocks)) {
                    sections[index].blocks = [];
                }

                nextSortOrder = this.getNextBlockSortOrder(sections[index].blocks);

                sections[index].blocks.push({
                    block_id: String(block.block_id || ("block_" + nextSortOrder)),
                    block_type: String(block.block_type || "generic"),
                    source_type: String(block.source_type || ""),
                    source_id: parseInt(block.source_id || 0, 10) || 0,
                    sort_order: parseInt(block.sort_order || nextSortOrder, 10) || nextSortOrder,
                    settings: $.isPlainObject(block.settings) ? block.settings : {}
                });

                break;
            }

            this.setSections(sections);

            return this.getLayout();
        },

        /**
         * Removes a block from a section.
         *
         * @param {string} sectionId Section ID.
         * @param {string} blockId Block ID.
         * @returns {Object}
         */
        removeBlockFromSection: function (sectionId, blockId) {
            var sections = this.getSections();
            var targetSectionId = String(sectionId || "");
            var targetBlockId = String(blockId || "");
            var index;

            for (index = 0; index < sections.length; index += 1) {
                if (String(sections[index].section_id || "") !== targetSectionId) {
                    continue;
                }

                sections[index].blocks = $.grep(sections[index].blocks || [], function (block) {
                    return String(block.block_id || "") !== targetBlockId;
                });

                break;
            }

            this.setSections(sections);

            return this.getLayout();
        },

        /**
         * Returns a flat block map keyed by block ID.
         *
         * @returns {Object}
         */
        getBlockMap: function () {
            var map = {};
            var sections = this.getSections();

            $.each(sections, function (sectionIndex, section) {
                $.each(section.blocks || [], function (blockIndex, block) {
                    var blockId = String(block.block_id || "");

                    if (blockId !== "") {
                        map[blockId] = block;
                    }
                });
            });

            return map;
        },

        /**
         * Makes an AJAX request through the shared admin router.
         *
         * @param {Object} payload AJAX payload.
         * @param {Function} onSuccess Success callback.
         * @param {Function} onError Error callback.
         * @returns {jqXHR}
         */
        request: function (payload, onSuccess, onError) {
            payload = payload || {};
            onSuccess = $.isFunction(onSuccess) ? onSuccess : function () {};
            onError = $.isFunction(onError) ? onError : function () {};

            return $.ajax({
                url: "WICore/WIClass/WIAjax.php",
                type: "POST",
                dataType: "json",
                data: payload
            }).done(function (response) {
                onSuccess(response || {});
            }).fail(function (xhr) {
                onError(xhr);
            });
        },

        /**
         * Renders a simple summary strip if present.
         *
         * @returns {void}
         */
        renderSummary: function () {
            var $summary = $(this.selectors.summary);
            var layout;
            var sectionCount;
            var blockCount;

            if (!$summary.length) {
                return;
            }

            layout = this.getLayout();
            sectionCount = $.isArray(layout.sections) ? layout.sections.length : 0;
            blockCount = this.countBlocks(layout.sections || []);

            $summary.text(
                sectionCount + " section(s), " +
                blockCount + " block(s)" +
                (this.state.isDirty ? " • Unsaved changes" : "")
            );
        },

        /**
         * Writes a builder message if a target exists.
         *
         * @param {string} message Message text.
         * @param {string} type Message type.
         * @returns {void}
         */
        setMessage: function (message, type) {
            var $target = $(this.selectors.messageTarget);
            var safeMessage = this.escapeHtml(message || "");
            var safeType = this.escapeHtml(type || "info");

            if (!$target.length) {
                return;
            }

            $target.html(
                '<div class="wi-builder-message wi-builder-message-' + safeType + '">' +
                    safeMessage +
                '</div>'
            );
        },

        /**
         * Clears the builder message area.
         *
         * @returns {void}
         */
        clearMessage: function () {
            $(this.selectors.messageTarget).empty();
        },

        /**
         * Normalises a layout payload.
         *
         * @param {Object} layout Raw layout.
         * @returns {Object}
         */
        normaliseLayout: function (layout) {
            var normalisedSections = [];
            var sectionSort = 10;

            layout = $.isPlainObject(layout) ? layout : {};

            $.each(layout.sections || [], function (sectionIndex, section) {
                var blocks = [];
                var blockSort = 10;

                if (!$.isPlainObject(section)) {
                    return;
                }

                $.each(section.blocks || [], function (blockIndex, block) {
                    if (!$.isPlainObject(block)) {
                        return;
                    }

                    blocks.push({
                        block_id: String(block.block_id || ("block_" + blockSort)),
                        block_type: String(block.block_type || "generic"),
                        source_type: String(block.source_type || ""),
                        source_id: parseInt(block.source_id || 0, 10) || 0,
                        sort_order: parseInt(block.sort_order || blockSort, 10) || blockSort,
                        settings: $.isPlainObject(block.settings) ? block.settings : {}
                    });

                    blockSort += 10;
                });

                normalisedSections.push({
                    section_id: String(section.section_id || ("section_" + sectionSort)),
                    section_code: String(section.section_code || ("section_" + sectionSort)),
                    section_title: String(section.section_title || "Section"),
                    section_description: String(section.section_description || ""),
                    sort_order: parseInt(section.sort_order || sectionSort, 10) || sectionSort,
                    blocks: blocks
                });

                sectionSort += 10;
            });

            normalisedSections.sort(function (left, right) {
                return (parseInt(left.sort_order || 0, 10) || 0) - (parseInt(right.sort_order || 0, 10) || 0);
            });

            return {
                layout_version: String(layout.layout_version || "1.0.0"),
                builder_type: String(layout.builder_type || this.state.builderType || "generic"),
                meta: $.isPlainObject(layout.meta) ? layout.meta : {},
                sections: normalisedSections
            };
        },

        /**
         * Returns the next section sort order.
         *
         * @param {Array} sections Section rows.
         * @returns {number}
         */
        getNextSectionSortOrder: function (sections) {
            var maxSort = 0;

            $.each(sections || [], function (index, section) {
                var sortOrder = parseInt(section.sort_order || 0, 10) || 0;
                if (sortOrder > maxSort) {
                    maxSort = sortOrder;
                }
            });

            return maxSort + 10;
        },

        /**
         * Returns the next block sort order.
         *
         * @param {Array} blocks Block rows.
         * @returns {number}
         */
        getNextBlockSortOrder: function (blocks) {
            var maxSort = 0;

            $.each(blocks || [], function (index, block) {
                var sortOrder = parseInt(block.sort_order || 0, 10) || 0;
                if (sortOrder > maxSort) {
                    maxSort = sortOrder;
                }
            });

            return maxSort + 10;
        },

        /**
         * Counts blocks across all sections.
         *
         * @param {Array} sections Section rows.
         * @returns {number}
         */
        countBlocks: function (sections) {
            var total = 0;

            $.each(sections || [], function (index, section) {
                total += (section.blocks || []).length;
            });

            return total;
        },

        /**
         * Returns the current date/time string.
         *
         * @returns {string}
         */
        getCurrentDateTimeString: function () {
            var now = new Date();
            return now.getFullYear() + "-" +
                this.pad(now.getMonth() + 1) + "-" +
                this.pad(now.getDate()) + " " +
                this.pad(now.getHours()) + ":" +
                this.pad(now.getMinutes()) + ":" +
                this.pad(now.getSeconds());
        },

        /**
         * Pads a number to 2 digits.
         *
         * @param {number} value Numeric value.
         * @returns {string}
         */
        pad: function (value) {
            value = parseInt(value, 10) || 0;
            return value < 10 ? "0" + value : String(value);
        },

        /**
         * Escapes HTML.
         *
         * @param {string} value Raw value.
         * @returns {string}
         */
        escapeHtml: function (value) {
            return $("<div/>").text(String(value || "")).html();
        }
    };

    window.WIBuilder = WIBuilder;
})(jQuery);