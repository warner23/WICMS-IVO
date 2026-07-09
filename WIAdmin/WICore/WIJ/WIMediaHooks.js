/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: WIMediaHooks.js
| Location: /root/WIAdmin/WICore/WIJ/WIMediaHooks.js
| Type: JavaScript Helper
| Layer: Shared Admin UI Hook Layer
| Purpose Area: Shared Media Source Picker / Upload / Attach Caller
| Version: 1.0.0
| Created: 2026-05-11
| Last Updated: 2026-05-11
| Status: WM-HOOK-01 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia hook layer for every WI system.
| - Opens a reusable media source modal from data attributes or JS
| - Allows camera capture, device upload, drag/drop upload, or library select
| - Uses WIMedia.js for upload, link, list, view and download
| - Contains no database logic, no storage logic and no compliance business logic
| - Compliance/Documents/Equipment/Checklist Evidence should call this layer only
|--------------------------------------------------------------------------
*/

(function (window, document, $) {
    'use strict';

    if (!$) {
        if (window.console && window.console.error) {
            window.console.error('WIMediaHooks.js requires jQuery.');
        }
        return;
    }

    var WIMediaHooks = {
        modalId: 'wiMediaHookModal',
        activeContext: {},
        activeOptions: {},
        lastLibraryItems: [],

        defaults: {
            title: 'Add media',
            system_code: 'wicms',
            entity_type: '',
            entity_id: 0,
            link_type: 'evidence',
            org_business_id: 0,
            org_site_id: 0,
            org_department_id: 0,
            folder: '',
            visibility: 'private',
            access_scope: 'business',
            is_private: 1,
            is_sensitive: 0,
            accept: '*/*',
            allow_camera: true,
            allow_upload: true,
            allow_drag_drop: true,
            allow_library: true,
            multiple: true,
            auto_attach_uploads: true
        },

        init: function () {
            this.ensureModal();
            this.bindGlobalTriggers();
            this.bindModalEvents();
        },

        /**
         * Opens the WIMedia hook modal from JS.
         *
         * @param {Object} options Hook options/context.
         */
        open: function (options) {
            if (!window.WIMedia || typeof window.WIMedia.upload !== 'function') {
                this.message('WIMedia.js is not loaded. Please load WIMedia.js before WIMediaHooks.js.', 'error');
                return;
            }

            this.activeOptions = $.extend({}, this.defaults, options || {});
            this.activeContext = this.normaliseContext(this.activeOptions);
            this.lastLibraryItems = [];

            this.renderModal();
            this.showModal();
            this.loadAttachedList();
        },

        close: function () {
            $('#' + this.modalId).removeClass('is-open').attr('aria-hidden', 'true');
            $('body').removeClass('wi-media-hook-open');
        },

        ensureModal: function () {
            if ($('#' + this.modalId).length) {
                return;
            }

            $('body').append(
                '<div id="' + this.modalId + '" class="wi-media-hook-modal" aria-hidden="true">' +
                    '<div class="wi-media-hook-backdrop" data-wimedia-hook-close></div>' +
                    '<div class="wi-media-hook-dialog" role="dialog" aria-modal="true" aria-labelledby="wiMediaHookTitle">' +
                        '<header class="wi-media-hook-header">' +
                            '<div>' +
                                '<p class="wi-media-hook-kicker">WIMedia</p>' +
                                '<h3 id="wiMediaHookTitle" data-wimedia-hook-title>Add media</h3>' +
                                '<small data-wimedia-hook-subtitle>Choose where the file should come from.</small>' +
                            '</div>' +
                            '<button type="button" class="wi-media-hook-close" data-wimedia-hook-close aria-label="Close">×</button>' +
                        '</header>' +

                        '<section class="wi-media-hook-body">' +
                            '<div class="wi-media-hook-options" data-wimedia-hook-options></div>' +

                            '<div class="wi-media-hook-panel" data-wimedia-hook-panel="camera" hidden>' +
                                '<h4>Take photo</h4>' +
                                '<p>Use the device camera where supported.</p>' +
                                '<input type="file" accept="image/*" capture="environment" data-wimedia-hook-camera-input>' +
                            '</div>' +

                            '<div class="wi-media-hook-panel" data-wimedia-hook-panel="upload" hidden>' +
                                '<h4>Upload from device</h4>' +
                                '<p>Select one or more files from this device.</p>' +
                                '<input type="file" data-wimedia-hook-upload-input>' +
                            '</div>' +

                            '<div class="wi-media-hook-panel" data-wimedia-hook-panel="drop" hidden>' +
                                '<h4>Drag and drop</h4>' +
                                '<div class="wi-media-hook-dropzone" data-wimedia-hook-dropzone>' +
                                    '<strong>Drop files here</strong>' +
                                    '<span>or click to choose files</span>' +
                                    '<input type="file" data-wimedia-hook-drop-input>' +
                                '</div>' +
                            '</div>' +

                            '<div class="wi-media-hook-panel" data-wimedia-hook-panel="library" hidden>' +
                                '<div class="wi-media-hook-library-head">' +
                                    '<div>' +
                                        '<h4>Choose from WIMedia</h4>' +
                                        '<p>Select an existing media item and attach it to this record.</p>' +
                                    '</div>' +
                                    '<button type="button" class="wi-btn wi-btn-light wi-btn-sm" data-wimedia-hook-refresh-library>Refresh</button>' +
                                '</div>' +
                                '<div class="wi-media-hook-library" data-wimedia-hook-library></div>' +
                            '</div>' +

                            '<div class="wi-media-hook-attached-wrap">' +
                                '<div class="wi-media-hook-attached-head">' +
                                    '<h4>Attached media</h4>' +
                                    '<button type="button" class="wi-btn wi-btn-light wi-btn-sm" data-wimedia-hook-refresh-attached>Refresh</button>' +
                                '</div>' +
                                '<div class="wi-media-hook-attached" data-wimedia-hook-attached></div>' +
                            '</div>' +

                            '<div class="wi-media-hook-status" data-wimedia-hook-status></div>' +
                        '</section>' +
                    '</div>' +
                '</div>'
            );
        },

        bindGlobalTriggers: function () {
            var self = this;

            $(document)
                .off('click.wimediaHooksOpen')
                .on('click.wimediaHooksOpen', '[data-wimedia-hook]', function (event) {
                    event.preventDefault();
                    self.open(self.optionsFromElement(this));
                });
        },

        bindModalEvents: function () {
            var self = this;

            $(document)
                .off('click.wimediaHooksClose')
                .on('click.wimediaHooksClose', '[data-wimedia-hook-close]', function (event) {
                    event.preventDefault();
                    self.close();
                });

            $(document)
                .off('click.wimediaHooksOption')
                .on('click.wimediaHooksOption', '[data-wimedia-hook-source]', function (event) {
                    event.preventDefault();
                    self.showPanel($(this).attr('data-wimedia-hook-source'));
                });

            $(document)
                .off('change.wimediaHooksCamera')
                .on('change.wimediaHooksCamera', '[data-wimedia-hook-camera-input]', function () {
                    self.uploadFiles(this.files || [], 'camera');
                    this.value = '';
                });

            $(document)
                .off('change.wimediaHooksUpload')
                .on('change.wimediaHooksUpload', '[data-wimedia-hook-upload-input]', function () {
                    self.uploadFiles(this.files || [], 'upload');
                    this.value = '';
                });

            $(document)
                .off('click.wimediaHooksDropInputClick')
                .on('click.wimediaHooksDropInputClick', '[data-wimedia-hook-dropzone]', function (event) {
                    if ($(event.target).is('input')) {
                        return;
                    }

                    $(this).find('[data-wimedia-hook-drop-input]').trigger('click');
                });

            $(document)
                .off('change.wimediaHooksDropInput')
                .on('change.wimediaHooksDropInput', '[data-wimedia-hook-drop-input]', function () {
                    self.uploadFiles(this.files || [], 'drag_drop');
                    this.value = '';
                });

            $(document)
                .off('dragover.wimediaHooksDrop dragenter.wimediaHooksDrop')
                .on('dragover.wimediaHooksDrop dragenter.wimediaHooksDrop', '[data-wimedia-hook-dropzone]', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).addClass('is-dragging');
                });

            $(document)
                .off('dragleave.wimediaHooksDrop dragend.wimediaHooksDrop')
                .on('dragleave.wimediaHooksDrop dragend.wimediaHooksDrop', '[data-wimedia-hook-dropzone]', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).removeClass('is-dragging');
                });

            $(document)
                .off('drop.wimediaHooksDrop')
                .on('drop.wimediaHooksDrop', '[data-wimedia-hook-dropzone]', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    $(this).removeClass('is-dragging');

                    var files = event.originalEvent && event.originalEvent.dataTransfer
                        ? event.originalEvent.dataTransfer.files
                        : [];

                    self.uploadFiles(files || [], 'drag_drop');
                });

            $(document)
                .off('click.wimediaHooksRefreshLibrary')
                .on('click.wimediaHooksRefreshLibrary', '[data-wimedia-hook-refresh-library]', function (event) {
                    event.preventDefault();
                    self.loadLibrary();
                });

            $(document)
                .off('click.wimediaHooksRefreshAttached')
                .on('click.wimediaHooksRefreshAttached', '[data-wimedia-hook-refresh-attached]', function (event) {
                    event.preventDefault();
                    self.loadAttachedList();
                });

            $(document)
                .off('click.wimediaHooksAttachLibrary')
                .on('click.wimediaHooksAttachLibrary', '[data-wimedia-hook-attach-media]', function (event) {
                    event.preventDefault();
                    self.attachExisting(parseInt($(this).attr('data-wimedia-hook-attach-media') || '0', 10));
                });

            $(document)
                .off('click.wimediaHooksView')
                .on('click.wimediaHooksView', '[data-wimedia-hook-view]', function (event) {
                    event.preventDefault();

                    var mediaId = parseInt($(this).attr('data-wimedia-hook-view') || '0', 10);
                    if (mediaId > 0 && window.WIMedia && typeof window.WIMedia.openView === 'function') {
                        window.WIMedia.openView(mediaId, self.activeContext);
                    }
                });

            $(document)
                .off('click.wimediaHooksDownload')
                .on('click.wimediaHooksDownload', '[data-wimedia-hook-download]', function (event) {
                    event.preventDefault();

                    var mediaId = parseInt($(this).attr('data-wimedia-hook-download') || '0', 10);
                    if (mediaId > 0 && window.WIMedia && typeof window.WIMedia.openDownload === 'function') {
                        window.WIMedia.openDownload(mediaId, self.activeContext);
                    }
                });
        },

        renderModal: function () {
            var opts = this.activeOptions;

            $('[data-wimedia-hook-title]').text(opts.title || 'Add media');
            $('[data-wimedia-hook-subtitle]').text(this.describeContext(this.activeContext));

            this.renderOptions();
            this.configureInputs();
            this.clearPanels();
            this.setStatus('');
        },

        renderOptions: function () {
            var opts = this.activeOptions;
            var html = '';

            if (this.truthy(opts.allow_camera)) {
                html += this.optionButton('camera', '📷', 'Camera', 'Take photo now');
            }

            if (this.truthy(opts.allow_upload)) {
                html += this.optionButton('upload', '⬆️', 'Upload', 'Choose from device');
            }

            if (this.truthy(opts.allow_drag_drop)) {
                html += this.optionButton('drop', '📥', 'Drop files', 'Drag and drop');
            }

            if (this.truthy(opts.allow_library)) {
                html += this.optionButton('library', '🗂️', 'WIMedia', 'Choose existing');
            }

            $('[data-wimedia-hook-options]').html(html);
        },

        optionButton: function (source, icon, title, text) {
            return '<button type="button" class="wi-media-hook-option" data-wimedia-hook-source="' + this.escapeAttr(source) + '">' +
                '<span>' + icon + '</span>' +
                '<strong>' + this.escapeHtml(title) + '</strong>' +
                '<small>' + this.escapeHtml(text) + '</small>' +
            '</button>';
        },

        configureInputs: function () {
            var opts = this.activeOptions;
            var accept = opts.accept || '*/*';
            var multiple = this.truthy(opts.multiple);

            $('[data-wimedia-hook-upload-input], [data-wimedia-hook-drop-input]')
                .attr('accept', accept)
                .prop('multiple', multiple);

            $('[data-wimedia-hook-camera-input]')
                .attr('accept', 'image/*')
                .prop('multiple', false);
        },

        showModal: function () {
            $('#' + this.modalId).addClass('is-open').attr('aria-hidden', 'false');
            $('body').addClass('wi-media-hook-open');
        },

        clearPanels: function () {
            $('[data-wimedia-hook-panel]').attr('hidden', true);
        },

        showPanel: function (panel) {
            this.clearPanels();
            $('[data-wimedia-hook-panel="' + this.escapeSelector(panel) + '"]').removeAttr('hidden');

            if (panel === 'library') {
                this.loadLibrary();
            }
        },

        uploadFiles: function (files, sourceType) {
            var fileList = this.normaliseFiles(files);

            if (!fileList.length) {
                this.setStatus('No files selected.', 'warning');
                return;
            }

            this.setStatus('Uploading ' + fileList.length + ' file(s)...', 'info');

            var context = $.extend({}, this.activeContext, {
                media_source: sourceType || 'upload'
            });

            var uploadRequest = fileList.length > 1
                ? window.WIMedia.uploadBulk(fileList, context)
                : window.WIMedia.upload(fileList[0], context);

            var self = this;

            uploadRequest
                .done(function (response) {
                    var uploadedIds = self.extractUploadedMediaIds(response);

                    self.setStatus(response.message || 'Upload complete.', 'success');

                    self.emit('uploaded', {
                        response: response,
                        media_ids: uploadedIds,
                        context: context
                    });

                    self.loadAttachedList();
                })
                .fail(function (xhr) {
                    self.setStatus('Upload failed. Check file size/type and server upload limits.', 'error');
                    self.emit('uploadFailed', {
                        xhr: xhr,
                        context: context
                    });
                });
        },

        attachExisting: function (mediaId) {
            var self = this;

            if (mediaId <= 0) {
                this.setStatus('A valid media item is required.', 'error');
                return;
            }

            if (!this.canAttach(this.activeContext)) {
                this.setStatus('This hook is missing entity_type, entity_id, system_code or link_type.', 'error');
                return;
            }

            this.setStatus('Attaching media...', 'info');

            window.WIMedia.attach(mediaId, this.activeContext)
                .done(function (response) {
                    self.setStatus(response.message || 'Media attached.', 'success');

                    self.emit('attached', {
                        response: response,
                        media_id: mediaId,
                        context: self.activeContext
                    });

                    self.loadAttachedList();
                })
                .fail(function (xhr) {
                    self.setStatus('Media attach failed.', 'error');
                    self.emit('attachFailed', {
                        xhr: xhr,
                        media_id: mediaId,
                        context: self.activeContext
                    });
                });
        },

        loadLibrary: function () {
            var self = this;
            var library = $('[data-wimedia-hook-library]');

            library.html('<div class="wi-media-hook-empty">Loading WIMedia library...</div>');

            window.WIMedia.list({
                media_type: this.activeOptions.media_type || '',
                search: this.activeOptions.search || '',
                org_business_id: this.activeContext.org_business_id || '',
                org_site_id: this.activeContext.org_site_id || '',
                access_scope: this.activeContext.access_scope || ''
            }).done(function (response) {
                var items = self.responseItems(response);
                self.lastLibraryItems = items;
                self.renderLibrary(items);
            }).fail(function () {
                library.html('<div class="wi-media-hook-empty is-error">Could not load WIMedia library.</div>');
            });
        },

        renderLibrary: function (items) {
            var html = '';

            if (!items.length) {
                $('[data-wimedia-hook-library]').html('<div class="wi-media-hook-empty">No media found.</div>');
                return;
            }

            items.forEach(function (item) {
                var id = parseInt(item.id || item.media_id || 0, 10);
                var title = item.title || item.original_name || item.file_name || ('Media #' + id);
                var type = item.media_type || item.file_type || 'file';

                html += '<article class="wi-media-hook-library-card">' +
                    '<div>' +
                        '<strong>' + WIMediaHooks.escapeHtml(title) + '</strong>' +
                        '<span>' + WIMediaHooks.escapeHtml(type) + '</span>' +
                    '</div>' +
                    '<div class="wi-media-hook-card-actions">' +
                        '<button type="button" data-wimedia-hook-view="' + id + '">View</button>' +
                        '<button type="button" data-wimedia-hook-attach-media="' + id + '">Attach</button>' +
                    '</div>' +
                '</article>';
            });

            $('[data-wimedia-hook-library]').html(html);
        },

        loadAttachedList: function () {
            var self = this;
            var box = $('[data-wimedia-hook-attached]');

            if (!this.canAttach(this.activeContext)) {
                box.html('<div class="wi-media-hook-empty">No record selected yet.</div>');
                return;
            }

            box.html('<div class="wi-media-hook-empty">Loading attached media...</div>');

            window.WIMedia.linked(this.activeContext)
                .done(function (response) {
                    self.renderAttached(self.responseItems(response));
                })
                .fail(function () {
                    box.html('<div class="wi-media-hook-empty is-error">Could not load attached media.</div>');
                });
        },

        renderAttached: function (items) {
            var html = '';

            if (!items.length) {
                $('[data-wimedia-hook-attached]').html('<div class="wi-media-hook-empty">No media attached yet.</div>');
                return;
            }

            items.forEach(function (item) {
                var id = parseInt(item.id || item.media_id || 0, 10);
                var title = item.title || item.original_name || item.file_name || ('Media #' + id);
                var type = item.media_type || item.file_type || 'file';

                html += '<article class="wi-media-hook-attached-card">' +
                    '<div>' +
                        '<strong>' + WIMediaHooks.escapeHtml(title) + '</strong>' +
                        '<span>' + WIMediaHooks.escapeHtml(type) + '</span>' +
                    '</div>' +
                    '<div class="wi-media-hook-card-actions">' +
                        '<button type="button" data-wimedia-hook-view="' + id + '">View</button>' +
                        '<button type="button" data-wimedia-hook-download="' + id + '">Download</button>' +
                    '</div>' +
                '</article>';
            });

            $('[data-wimedia-hook-attached]').html(html);
        },

        optionsFromElement: function (element) {
            var node = $(element);

            return {
                title: node.attr('data-wimedia-title') || node.attr('title') || this.defaults.title,

                system_code: node.attr('data-wimedia-system-code') || node.attr('data-system-code') || this.defaults.system_code,

                entity_type: node.attr('data-wimedia-entity-type') ||
                    node.attr('data-wimedia-target-type') ||
                    node.attr('data-entity-type') ||
                    node.attr('data-target-type') ||
                    '',

                entity_id: this.intAttr(node, 'data-wimedia-entity-id') ||
                    this.intAttr(node, 'data-wimedia-target-id') ||
                    this.intAttr(node, 'data-entity-id') ||
                    this.intAttr(node, 'data-target-id') ||
                    0,

                link_type: node.attr('data-wimedia-link-type') ||
                    node.attr('data-wimedia-context') ||
                    node.attr('data-link-type') ||
                    this.defaults.link_type,

                org_business_id: this.intAttr(node, 'data-wimedia-org-business-id') ||
                    this.intAttr(node, 'data-org-business-id') ||
                    this.intAttr(node, 'data-business-id') ||
                    0,

                org_site_id: this.intAttr(node, 'data-wimedia-org-site-id') ||
                    this.intAttr(node, 'data-org-site-id') ||
                    this.intAttr(node, 'data-site-id') ||
                    0,

                org_department_id: this.intAttr(node, 'data-wimedia-org-department-id') ||
                    this.intAttr(node, 'data-org-department-id') ||
                    this.intAttr(node, 'data-department-id') ||
                    0,

                folder: node.attr('data-wimedia-folder') || '',
                accept: node.attr('data-wimedia-accept') || this.defaults.accept,

                allow_camera: this.boolAttr(node, 'data-wimedia-allow-camera', this.defaults.allow_camera),
                allow_upload: this.boolAttr(node, 'data-wimedia-allow-upload', this.defaults.allow_upload),
                allow_drag_drop: this.boolAttr(node, 'data-wimedia-allow-drag-drop', this.defaults.allow_drag_drop),
                allow_library: this.boolAttr(node, 'data-wimedia-allow-library', this.defaults.allow_library),
                multiple: this.boolAttr(node, 'data-wimedia-multiple', this.defaults.multiple),

                visibility: node.attr('data-wimedia-visibility') || this.defaults.visibility,
                access_scope: node.attr('data-wimedia-access-scope') || this.defaults.access_scope,
                is_private: this.boolAttr(node, 'data-wimedia-private', true) ? 1 : 0,
                is_sensitive: this.boolAttr(node, 'data-wimedia-sensitive', false) ? 1 : 0
            };
        },

        normaliseContext: function (options) {
            return {
                system_code: options.system_code || 'wicms',
                entity_type: options.entity_type || '',
                entity_id: parseInt(options.entity_id || 0, 10) || 0,
                link_type: options.link_type || 'evidence',

                org_business_id: parseInt(options.org_business_id || 0, 10) || 0,
                org_site_id: parseInt(options.org_site_id || 0, 10) || 0,
                org_department_id: parseInt(options.org_department_id || 0, 10) || 0,

                folder: options.folder || '',
                visibility: options.visibility || 'private',
                access_scope: options.access_scope || 'business',
                is_private: this.truthy(options.is_private) ? 1 : 0,
                is_sensitive: this.truthy(options.is_sensitive) ? 1 : 0
            };
        },

        canAttach: function (context) {
            return !!(
                context &&
                context.system_code &&
                context.entity_type &&
                parseInt(context.entity_id || 0, 10) > 0 &&
                context.link_type
            );
        },

        describeContext: function (context) {
            if (!this.canAttach(context)) {
                return 'Upload or choose media. Attachment will require a selected record.';
            }

            return 'Attach to ' + context.entity_type + ' #' + context.entity_id + ' as ' + context.link_type + '.';
        },

        normaliseFiles: function (files) {
            var list = [];
            var i;

            if (!files) {
                return list;
            }

            for (i = 0; i < files.length; i += 1) {
                list.push(files[i]);
            }

            return list;
        },

        extractUploadedMediaIds: function (response) {
            var ids = [];
            var items = [];

            if (!response) {
                return ids;
            }

            if (response.media_id) {
                ids.push(parseInt(response.media_id, 10));
            }

            if (response.data && response.data.media_id) {
                ids.push(parseInt(response.data.media_id, 10));
            }

            if ($.isArray(response.items)) {
                items = response.items;
            } else if (response.data && $.isArray(response.data.items)) {
                items = response.data.items;
            }

            items.forEach(function (item) {
                if (item && item.media_id) {
                    ids.push(parseInt(item.media_id, 10));
                } else if (item && item.data && item.data.media_id) {
                    ids.push(parseInt(item.data.media_id, 10));
                }
            });

            return ids.filter(function (id) {
                return id > 0;
            });
        },

        responseItems: function (response) {
            if (!response) {
                return [];
            }

            if ($.isArray(response.items)) {
                return response.items;
            }

            if (response.data && $.isArray(response.data.items)) {
                return response.data.items;
            }

            if (response.data && $.isArray(response.data.data)) {
                return response.data.data;
            }

            if ($.isArray(response.data)) {
                return response.data;
            }

            return [];
        },

        setStatus: function (message, type) {
            var box = $('[data-wimedia-hook-status]');
            type = type || 'info';

            if (!message) {
                box.removeClass('is-error is-success is-warning is-info').text('');
                return;
            }

            box
                .removeClass('is-error is-success is-warning is-info')
                .addClass('is-' + type)
                .text(message);
        },

        message: function (message, type) {
            this.setStatus(message, type || 'info');

            if (window.WINotify && typeof window.WINotify.show === 'function') {
                window.WINotify.show(message, type || 'info');
            }
        },

        emit: function (name, payload) {
            $(document).trigger('wimediahooks:' + name, [payload || {}]);
        },

        intAttr: function (node, attr) {
            return parseInt(node.attr(attr) || '0', 10) || 0;
        },

        boolAttr: function (node, attr, fallback) {
            if (typeof node.attr(attr) === 'undefined') {
                return fallback;
            }

            return this.truthy(node.attr(attr));
        },

        truthy: function (value) {
            return value === true || value === 1 || value === '1' || value === 'true' || value === 'yes';
        },

        escapeHtml: function (value) {
            return String(value === null || typeof value === 'undefined' ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        escapeAttr: function (value) {
            return this.escapeHtml(value);
        },

        escapeSelector: function (value) {
            if (window.CSS && typeof window.CSS.escape === 'function') {
                return window.CSS.escape(String(value || ''));
            }

            return String(value || '').replace(/"/g, '\\"');
        }
    };

    window.WIMediaHooks = WIMediaHooks;

    $(function () {
        window.WIMediaHooks.init();
    });
})(window, document, window.jQuery);