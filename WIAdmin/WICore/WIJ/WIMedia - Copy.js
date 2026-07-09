/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Script: WIMedia
| File: WIMedia.js
| Location: /WIAdmin/WICore/WIJ/WIMedia.js
| Type: Shared Media JavaScript Controller
| Layer: UI Controller
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Site-wide JavaScript bridge for the canonical WIMedia service. This handles
| modal loading, upload submission, media listing, selecting, previewing, and
| linking. It does not contain upload business logic.
|--------------------------------------------------------------------------
*/

(function (window, document, $) {
    'use strict';

    var WIMedia = {
        ajaxUrl: 'WICore/WIClass/WIAjax.php',
        modalMount: 'WIMediaModalMount',
        selectedMediaId: 0,
        currentContext: {},
        currentItems: [],
        currentIndex: 0,

        /**
         * Initialises global media handlers.
         *
         * @return {void}
         */
        init: function () {
            this.bindDocumentEvents();
            this.ensureModalMount();
        },

        /**
         * Opens the upload modal.
         *
         * @param {Object} context
         * @return {void}
         */
        openUploadModal: function (context) {
            this.openModal('media_modal_upload', context || {});
        },

        /**
         * Opens the picker modal.
         *
         * @param {Object} context
         * @return {void}
         */
        openPickerModal: function (context) {
            this.openModal('media_modal_picker', context || {}, function () {
                WIMedia.loadMedia(context || {});
            });
        },

        /**
         * Opens the viewer modal.
         *
         * @param {number} mediaId
         * @param {Object} context
         * @return {void}
         */
        openViewerModal: function (mediaId, context) {
            context = context || {};
            context.media_id = mediaId;
            this.openModal('media_modal_viewer', context);
        },

        /**
         * Opens the full media manager modal.
         *
         * @param {Object} context
         * @return {void}
         */
        openManagerModal: function (context) {
            this.openModal('media_modal_manager', context || {}, function () {
                WIMedia.loadDocuments(context || {});
                WIMedia.loadPlaylist(context || {});
                WIMedia.loadImages(context || {});
            });
        },

        /**
         * Loads a modal shell from WIAjax.
         *
         * @param {string} action
         * @param {Object} context
         * @param {Function=} callback
         * @return {void}
         */
        openModal: function (action, context, callback) {
            var payload = $.extend({}, context || {}, { action: action });
            this.currentContext = context || {};

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: payload,
                dataType: 'json'
            }).done(function (response) {
                var html = WIMedia.getResponseData(response).html || '';

                if (!html) {
                    WIMedia.notify('Unable to load media modal.', 'error');
                    return;
                }

                WIMedia.renderModal(html);
                WIMedia.bindModalEvents();

                if (typeof callback === 'function') {
                    callback(response);
                }
            }).fail(function () {
                WIMedia.notify('Media modal request failed.', 'error');
            });
        },

        /**
         * Renders modal HTML into the page and displays it.
         *
         * @param {string} html
         * @return {void}
         */
        renderModal: function (html) {
            var mount = this.ensureModalMount();
            mount.html(html);

            var modal = $('#wiMediaModal');
            modal.removeClass('hide').addClass('show').show();
            $('body').addClass('modal-open');
        },

        /**
         * Closes the media modal.
         *
         * @return {void}
         */
        closeModal: function () {
            $('#wiMediaModal').removeClass('show').addClass('hide').hide();
            $('body').removeClass('modal-open');
        },

        /**
         * Uploads selected files from the current modal.
         *
         * @param {HTMLElement} form
         * @return {void}
         */
        uploadFromForm: function (form) {
            var formData = new FormData(form);

            if (!formData.get('action')) {
                formData.append('action', 'media_upload_bulk');
            }

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                xhr: function () {
                    var xhr = $.ajaxSettings.xhr();
                    if (xhr.upload) {
                        xhr.upload.addEventListener('progress', function (evt) {
                            if (evt.lengthComputable) {
                                WIMedia.updateProgress(Math.round((evt.loaded / evt.total) * 100));
                            }
                        }, false);
                    }
                    return xhr;
                }
            }).done(function (response) {
                WIMedia.updateProgress(100);
                WIMedia.renderResult(response);
                WIMedia.loadLinked(WIMedia.currentContext);
            }).fail(function () {
                WIMedia.notify('Upload failed.', 'error');
            });
        },

        /**
         * Loads media using filters.
         *
         * @param {Object} filters
         * @return {void}
         */
        loadMedia: function (filters) {
            var payload = $.extend({}, filters || {}, { action: 'media_list' });

            $.ajax({
                url: this.ajaxUrl,
                type: 'GET',
                data: payload,
                dataType: 'json'
            }).done(function (response) {
                var items = WIMedia.getItems(response);
                WIMedia.currentItems = items;
                WIMedia.renderGrid(items, $('[data-wimedia-grid]'));
            }).fail(function () {
                WIMedia.notify('Unable to load media.', 'error');
            });
        },

        /**
         * Loads linked media for the active context.
         *
         * @param {Object} context
         * @return {void}
         */
        loadLinked: function (context) {
            var payload = $.extend({}, context || {}, { action: 'media_linked' });

            if (!payload.entity_type || !payload.entity_id) {
                return;
            }

            $.ajax({
                url: this.ajaxUrl,
                type: 'GET',
                data: payload,
                dataType: 'json'
            }).done(function (response) {
                WIMedia.renderList(WIMedia.getItems(response), $('[data-wimedia-linked-list]'));
            });
        },

        /**
         * Links the selected media item to the active context.
         *
         * @return {void}
         */
        linkSelected: function () {
            if (this.selectedMediaId <= 0) {
                this.notify('Select a media item first.', 'warning');
                return;
            }

            var payload = $.extend({}, this.currentContext, {
                action: 'media_link',
                media_id: this.selectedMediaId
            });

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: payload,
                dataType: 'json'
            }).done(function (response) {
                WIMedia.notify(response.message || 'Media linked.', 'success');
                WIMedia.loadLinked(WIMedia.currentContext);
            }).fail(function () {
                WIMedia.notify('Unable to link media.', 'error');
            });
        },

        /**
         * Loads document records into the manager.
         *
         * @param {Object} context
         * @return {void}
         */
        loadDocuments: function (context) {
            this.loadTypedMedia('document', context, $('[data-wimedia-documents]'));
        },

        /**
         * Loads audio/video records into the manager playlist.
         *
         * @param {Object} context
         * @return {void}
         */
        loadPlaylist: function (context) {
            var payload = $.extend({}, context || {}, { action: 'media_list' });
            var target = $('[data-wimedia-playlist]');

            $.ajax({
                url: this.ajaxUrl,
                type: 'GET',
                data: payload,
                dataType: 'json'
            }).done(function (response) {
                var items = WIMedia.getItems(response).filter(function (item) {
                    return item.media_type === 'video' || item.media_type === 'audio';
                });

                WIMedia.currentItems = items;
                WIMedia.renderPlaylist(items, target);
            });
        },

        /**
         * Loads image records into the manager.
         *
         * @param {Object} context
         * @return {void}
         */
        loadImages: function (context) {
            this.loadTypedMedia('image', context, $('[data-wimedia-images]'));
        },

        /**
         * Loads media of one type.
         *
         * @param {string} mediaType
         * @param {Object} context
         * @param {jQuery} target
         * @return {void}
         */
        loadTypedMedia: function (mediaType, context, target) {
            var payload = $.extend({}, context || {}, {
                action: 'media_list',
                media_type: mediaType
            });

            $.ajax({
                url: this.ajaxUrl,
                type: 'GET',
                data: payload,
                dataType: 'json'
            }).done(function (response) {
                WIMedia.renderGrid(WIMedia.getItems(response), target);
            });
        },

        /**
         * Binds global document-level handlers.
         *
         * @return {void}
         */
        bindDocumentEvents: function () {
            $(document).on('click', '[data-wimedia-open-upload]', function () {
                WIMedia.openUploadModal(WIMedia.readContext(this));
            });

            $(document).on('click', '[data-wimedia-open-picker]', function () {
                WIMedia.openPickerModal(WIMedia.readContext(this));
            });

            $(document).on('click', '[data-wimedia-open-manager]', function () {
                WIMedia.openManagerModal(WIMedia.readContext(this));
            });

            $(document).on('click', '[data-wimedia-open-viewer]', function () {
                WIMedia.openViewerModal(parseInt($(this).data('media-id'), 10) || 0, WIMedia.readContext(this));
            });
        },

        /**
         * Binds handlers inside the currently-open modal.
         *
         * @return {void}
         */
        bindModalEvents: function () {
            var modal = $('#wiMediaModal');

            modal.off('click.wimedia');
            modal.off('change.wimedia');
            modal.off('dragover.wimedia drop.wimedia');

            modal.on('click.wimedia', '[data-wimedia-close]', function () {
                WIMedia.closeModal();
            });

            modal.on('click.wimedia', '[data-wimedia-submit-upload]', function () {
                var form = modal.find('.wi-media-upload-form').get(0);
                if (form) {
                    WIMedia.uploadFromForm(form);
                }
            });

            modal.on('click.wimedia', '[data-wimedia-tab]', function () {
                WIMedia.switchTab($(this).data('wimedia-tab'));
            });

            modal.on('click.wimedia', '[data-wimedia-card]', function () {
                WIMedia.selectMedia($(this));
            });

            modal.on('click.wimedia', '[data-wimedia-link-selected]', function () {
                WIMedia.linkSelected();
            });

            modal.on('click.wimedia', '[data-wimedia-refresh]', function () {
                WIMedia.loadMedia(WIMedia.currentContext);
            });

            modal.on('click.wimedia', '[data-wimedia-load-documents]', function () {
                WIMedia.loadDocuments(WIMedia.currentContext);
            });

            modal.on('click.wimedia', '[data-wimedia-prev]', function () {
                WIMedia.stepPlayer(-1);
            });

            modal.on('click.wimedia', '[data-wimedia-next]', function () {
                WIMedia.stepPlayer(1);
            });

            modal.on('dragover.wimedia', '[data-wimedia-dropzone]', function (event) {
                event.preventDefault();
                $(this).addClass('is-dragover');
            });

            modal.on('drop.wimedia', '[data-wimedia-dropzone]', function (event) {
                event.preventDefault();
                $(this).removeClass('is-dragover');
                var input = $(this).find('[data-wimedia-file-input]').get(0);
                if (input && event.originalEvent.dataTransfer) {
                    input.files = event.originalEvent.dataTransfer.files;
                }
            });
        },

        /**
         * Switches visible modal tab.
         *
         * @param {string} tab
         * @return {void}
         */
        switchTab: function (tab) {
            $('[data-wimedia-tab]').removeClass('is-active');
            $('[data-wimedia-tab="' + tab + '"]').addClass('is-active');
            $('[data-wimedia-panel]').removeClass('is-active');
            $('[data-wimedia-panel="' + tab + '"]').addClass('is-active');
        },

        /**
         * Selects a media card.
         *
         * @param {jQuery} card
         * @return {void}
         */
        selectMedia: function (card) {
            $('[data-wimedia-card]').removeClass('is-selected');
            card.addClass('is-selected');
            this.selectedMediaId = parseInt(card.data('media-id'), 10) || 0;
        },

        /**
         * Renders a grid of media cards.
         *
         * @param {Array} items
         * @param {jQuery} target
         * @return {void}
         */
        renderGrid: function (items, target) {
            if (!target || target.length === 0) {
                return;
            }

            if (!items || items.length === 0) {
                target.html('<p class="wi-muted">No media found.</p>');
                return;
            }

            target.html(items.map(function (item) {
                return WIMedia.cardHtml(item);
            }).join(''));
        },

        /**
         * Renders a small linked media list.
         *
         * @param {Array} items
         * @param {jQuery} target
         * @return {void}
         */
        renderList: function (items, target) {
            if (!target || target.length === 0) {
                return;
            }

            if (!items || items.length === 0) {
                target.html('<p class="wi-muted">No linked media yet.</p>');
                return;
            }

            target.html(items.map(function (item) {
                return '<div class="wi-media-list-row" data-wimedia-open-viewer data-media-id="' + WIMedia.escape(item.id) + '">' +
                    '<strong>' + WIMedia.escape(item.title || item.original_name || 'Media') + '</strong>' +
                    '<span>' + WIMedia.escape(item.media_type || 'file') + '</span>' +
                    '</div>';
            }).join(''));
        },

        /**
         * Renders playlist and loads the first media item.
         *
         * @param {Array} items
         * @param {jQuery} target
         * @return {void}
         */
        renderPlaylist: function (items, target) {
            if (!target || target.length === 0) {
                return;
            }

            if (!items || items.length === 0) {
                target.html('<p class="wi-muted">No audio or video found.</p>');
                $('[data-wimedia-player]').html('');
                return;
            }

            target.html(items.map(function (item, index) {
                return '<button type="button" class="wi-media-playlist-item" data-wimedia-play-index="' + index + '">' +
                    WIMedia.escape(item.title || item.original_name || 'Media') +
                    '</button>';
            }).join(''));

            target.off('click.wimedia-play').on('click.wimedia-play', '[data-wimedia-play-index]', function () {
                WIMedia.currentIndex = parseInt($(this).data('wimedia-play-index'), 10) || 0;
                WIMedia.renderPlayer();
            });

            this.currentIndex = 0;
            this.renderPlayer();
        },

        /**
         * Renders current playlist item into player.
         *
         * @return {void}
         */
        renderPlayer: function () {
            var item = this.currentItems[this.currentIndex];
            var target = $('[data-wimedia-player]');
            var url;

            if (!item || target.length === 0) {
                return;
            }

            url = item.public_url || item.file_path || '';

            if (item.media_type === 'video') {
                target.html('<video src="' + this.escapeAttr(url) + '" controls></video>');
            } else {
                target.html('<audio src="' + this.escapeAttr(url) + '" controls></audio>');
            }
        },

        /**
         * Moves player by one step.
         *
         * @param {number} direction
         * @return {void}
         */
        stepPlayer: function (direction) {
            if (!this.currentItems.length) {
                return;
            }

            this.currentIndex += direction;

            if (this.currentIndex < 0) {
                this.currentIndex = this.currentItems.length - 1;
            }

            if (this.currentIndex >= this.currentItems.length) {
                this.currentIndex = 0;
            }

            this.renderPlayer();
        },

        /**
         * Builds a media card.
         *
         * @param {Object} item
         * @return {string}
         */
        cardHtml: function (item) {
            var title = this.escape(item.title || item.original_name || 'Media');
            var type = this.escape(item.media_type || 'file');
            var url = this.escapeAttr(item.public_url || item.file_path || '');
            var preview = '<div class="wi-media-card-icon">' + type + '</div>';

            if (item.media_type === 'image' && url) {
                preview = '<img src="' + url + '" alt="' + title + '">';
            }

            return '<button type="button" class="wi-media-card" data-wimedia-card data-media-id="' + this.escapeAttr(item.id || 0) + '">' +
                '<span class="wi-media-card-preview">' + preview + '</span>' +
                '<span class="wi-media-card-title">' + title + '</span>' +
                '<span class="wi-media-card-type">' + type + '</span>' +
                '</button>';
        },

        /**
         * Reads data attributes into a context object.
         *
         * @param {HTMLElement} element
         * @return {Object}
         */
        readContext: function (element) {
            var data = $(element).data();
            var context = {};
            var map = {
                systemCode: 'system_code',
                entityType: 'entity_type',
                entityId: 'entity_id',
                linkType: 'link_type',
                orgBusinessId: 'org_business_id',
                orgSiteId: 'org_site_id',
                orgDepartmentId: 'org_department_id'
            };

            Object.keys(map).forEach(function (key) {
                if (typeof data[key] !== 'undefined') {
                    context[map[key]] = data[key];
                }
            });

            return context;
        },

        /**
         * Ensures modal mount exists.
         *
         * @return {jQuery}
         */
        ensureModalMount: function () {
            var mount = $('#' + this.modalMount);

            if (mount.length === 0) {
                $('body').append('<div id="' + this.modalMount + '"></div>');
                mount = $('#' + this.modalMount);
            }

            return mount;
        },

        /**
         * Updates upload progress.
         *
         * @param {number} percent
         * @return {void}
         */
        updateProgress: function (percent) {
            $('[data-wimedia-progress]').prop('hidden', false);
            $('[data-wimedia-progress-bar]').css('width', percent + '%').text(percent + '%');
        },

        /**
         * Renders an upload result.
         *
         * @param {Object} response
         * @return {void}
         */
        renderResult: function (response) {
            var message = response.message || 'Upload complete.';
            $('[data-wimedia-result]').html('<div class="wi-media-result-message">' + this.escape(message) + '</div>');
        },

        /**
         * Extracts response data.
         *
         * @param {Object} response
         * @return {Object}
         */
        getResponseData: function (response) {
            return response && response.data ? response.data : {};
        },

        /**
         * Extracts item array from response.
         *
         * @param {Object} response
         * @return {Array}
         */
        getItems: function (response) {
            var data = this.getResponseData(response);
            return data.items || data.media || [];
        },

        /**
         * Shows a lightweight notification.
         *
         * @param {string} message
         * @param {string} type
         * @return {void}
         */
        notify: function (message, type) {
            if (window.WINotify && typeof window.WINotify.show === 'function') {
                window.WINotify.show(message, type || 'info');
                return;
            }

            if (window.console) {
                console.log('[WIMedia][' + (type || 'info') + '] ' + message);
            }
        },

        /**
         * Escapes HTML.
         *
         * @param {*} value
         * @return {string}
         */
        escape: function (value) {
            return String(value === null || typeof value === 'undefined' ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        /**
         * Escapes attribute values.
         *
         * @param {*} value
         * @return {string}
         */
        escapeAttr: function (value) {
            return this.escape(value);
        }
    };

    window.WIMedia = WIMedia;

    $(document).ready(function () {
        WIMedia.init();
    });
})(window, document, jQuery);
