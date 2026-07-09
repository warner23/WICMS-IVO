/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: WIMediaCenter.js
| Location: /root/WIAdmin/WICore/WIJ/WIMediaCenter.js
| Type: JavaScript Controller
| Layer: Admin UI Behaviour
| Purpose Area: Media Centre Page Controller
| Version: 1.2.0
| Created: 2026-05-04
| Last Updated: 2026-05-08
| Status: WM-02 Production Refactor
|--------------------------------------------------------------------------
| Summary:
| Media Centre page controller.
| - Controls the admin Media Centre page only
| - Uses WIMedia.js as the shared AJAX/media helper
| - Supports drag/drop multi-file upload, tab switching, filters, preview and linked lookup
| - Contains no database, upload storage, permission or compliance business logic
|--------------------------------------------------------------------------
*/

(function (window, document, $) {
    'use strict';

    if (!$) {
        if (window.console && window.console.error) {
            window.console.error('WIMediaCenter.js requires jQuery.');
        }
        return;
    }

    var WIMediaCenter = {
        rootSelector: '[data-wimedia-centre], [data-wi-media-centre], #wiMediaCentre',
        activeItems: [],
        activeIndex: 0,
        cache: {
            all: [],
            documents: [],
            images: [],
            media: [],
            linked: []
        },

        init: function (root) {
            var scope = root ? $(root) : $(this.rootSelector);

            if (!scope.length) {
                return;
            }

            if (!window.WIMedia || typeof window.WIMedia.request !== 'function') {
                this.message(scope, 'WIMedia.js is not loaded. Load WIMedia.js before WIMediaCenter.js.', 'error');
                return;
            }

            this.bind(scope);
            this.prepareUploadArea(scope);
            this.loadAll(scope);
        },

        bind: function (scope) {
            var self = this;

            scope
                .off('click.wimediaCenterTabs')
                .on('click.wimediaCenterTabs', '[data-wimedia-tab], [data-media-tab]', function (event) {
                    event.preventDefault();
                    self.switchTab(scope, self.attr(this, 'tab'));
                });

            scope
                .off('click.wimediaCenterRefresh')
                .on('click.wimediaCenterRefresh', '[data-wimedia-action="refresh"], [data-media-refresh], [data-media-action="refresh"]', function (event) {
                    event.preventDefault();
                    self.loadAll(scope);
                });

            scope
                .off('submit.wimediaCenterUpload')
                .on('submit.wimediaCenterUpload', '[data-wimedia-upload-form], [data-media-upload-form]', function (event) {
                    event.preventDefault();
                    self.uploadFromForm(scope, this);
                });

            scope
                .off('change.wimediaCenterInput')
                .on('change.wimediaCenterInput', '[data-wimedia-file-input], [data-media-file], input[type="file"][data-wimedia-upload-input]', function () {
                    self.updateSelectedFiles(scope, this.files || []);
                });

            scope
                .off('click.wimediaCenterApplyFilters')
                .on('click.wimediaCenterApplyFilters', '[data-media-apply-filters], [data-wimedia-apply-filters]', function (event) {
                    event.preventDefault();
                    self.loadAll(scope);
                });

            scope
                .off('keyup.wimediaCenterSearch change.wimediaCenterFilters')
                .on('keyup.wimediaCenterSearch change.wimediaCenterFilters', '[data-wimedia-filter], [data-media-filter-type], [data-media-filter-search], [data-media-filter-business], [data-media-filter-site], [data-media-filter-department]', function (event) {
                    if (event.type === 'keyup' && event.key && event.key !== 'Enter') {
                        return;
                    }

                    self.loadAll(scope);
                });

            scope
                .off('click.wimediaCenterLoadType')
                .on('click.wimediaCenterLoadType', '[data-media-load-type], [data-wimedia-load-type]', function (event) {
                    event.preventDefault();
                    self.loadType(scope, $(this).attr('data-media-load-type') || $(this).attr('data-wimedia-load-type'));
                });

            scope
                .off('click.wimediaCenterLoadLinked')
                .on('click.wimediaCenterLoadLinked', '[data-media-load-linked], [data-wimedia-load-linked]', function (event) {
                    event.preventDefault();
                    self.loadLinked(scope);
                });

            scope
                .off('click.wimediaCenterItem')
                .on('click.wimediaCenterItem', '[data-wimedia-item], [data-media-item]', function (event) {
                    if ($(event.target).is('button, a, input, select, textarea')) {
                        return;
                    }

                    event.preventDefault();
                    self.selectItem(scope, $(this).attr('data-wimedia-id') || $(this).attr('data-media-id'));
                });

            scope
                .off('click.wimediaCenterView')
                .on('click.wimediaCenterView', '[data-wimedia-centre-view], [data-media-view]', function (event) {
                    event.preventDefault();
                    self.openView(scope, $(this).attr('data-wimedia-centre-view') || $(this).attr('data-media-view'));
                });

            scope
                .off('click.wimediaCenterDownload')
                .on('click.wimediaCenterDownload', '[data-wimedia-centre-download], [data-media-download]', function (event) {
                    event.preventDefault();
                    self.openDownload(scope, $(this).attr('data-wimedia-centre-download') || $(this).attr('data-media-download'));
                });

            scope
                .off('click.wimediaCenterPrev')
                .on('click.wimediaCenterPrev', '[data-wimedia-player-prev], [data-media-player-prev]', function (event) {
                    event.preventDefault();
                    self.playRelative(scope, -1);
                });

            scope
                .off('click.wimediaCenterNext')
                .on('click.wimediaCenterNext', '[data-wimedia-player-next], [data-media-player-next]', function (event) {
                    event.preventDefault();
                    self.playRelative(scope, 1);
                });
        },

        prepareUploadArea: function (scope) {
            var self = this;
            var dropzone = scope.find('[data-wimedia-dropzone], [data-media-dropzone], .wi-media-dropzone').first();

            if (!dropzone.length) {
                return;
            }

            dropzone
                .off('click.wimediaDropzone keydown.wimediaDropzone')
                .on('click.wimediaDropzone keydown.wimediaDropzone', function (event) {
                    if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
                        return;
                    }

                    if ($(event.target).is('input, button, select, textarea, label, a')) {
                        return;
                    }

                    event.preventDefault();
                    scope.find('[data-wimedia-file-input], [data-media-file], input[type="file"][data-wimedia-upload-input]').first().trigger('click');
                });

            dropzone
                .off('dragenter.wimediaDropzone dragover.wimediaDropzone')
                .on('dragenter.wimediaDropzone dragover.wimediaDropzone', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    dropzone.addClass('is-dragging');
                });

            dropzone
                .off('dragleave.wimediaDropzone dragend.wimediaDropzone')
                .on('dragleave.wimediaDropzone dragend.wimediaDropzone', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    dropzone.removeClass('is-dragging');
                });

            dropzone
                .off('drop.wimediaDropzone')
                .on('drop.wimediaDropzone', function (event) {
                    var original = event.originalEvent || event;
                    var files = original.dataTransfer && original.dataTransfer.files ? original.dataTransfer.files : [];

                    event.preventDefault();
                    event.stopPropagation();
                    dropzone.removeClass('is-dragging');

                    if (!files.length) {
                        self.message(scope, 'No files were dropped.', 'warning');
                        return;
                    }

                    self.updateSelectedFiles(scope, files);
                    self.uploadFiles(scope, files, self.uploadContext(scope));
                });
        },

        switchTab: function (scope, tab) {
            if (!tab) {
                return;
            }

            scope.find('[data-wimedia-tab], [data-media-tab]').removeClass('is-active');
            scope.find('[data-wimedia-tab="' + tab + '"], [data-media-tab="' + tab + '"]').addClass('is-active');

            scope.find('[data-wimedia-panel], [data-media-tab-panel]').removeClass('is-active');
            scope.find('[data-wimedia-panel="' + tab + '"], [data-media-tab-panel="' + tab + '"]').addClass('is-active');
        },

        context: function (scope) {
            var value = scope.attr('data-wimedia-context') || scope.attr('data-context') || '{}';

            try {
                return JSON.parse(value);
            } catch (e) {
                return {};
            }
        },

        filters: function (scope, extra) {
            var filters = $.extend({}, this.context(scope), extra || {});
            var type = scope.find('[data-media-filter-type]').val();
            var search = scope.find('[data-media-filter-search]').val();
            var businessId = scope.find('[data-media-filter-business]').val();
            var siteId = scope.find('[data-media-filter-site]').val();
            var departmentId = scope.find('[data-media-filter-department]').val();

            scope.find('[data-wimedia-filter]').each(function () {
                var key = $(this).attr('data-wimedia-filter');
                var value = $(this).val();

                if (key && value !== '') {
                    filters[key] = value;
                }
            });

            if (type) {
                filters.media_type = type;
            }

            if (search) {
                filters.search = search;
            }

            if (businessId) {
                filters.org_business_id = businessId;
            }

            if (siteId) {
                filters.org_site_id = siteId;
            }

            if (departmentId) {
                filters.org_department_id = departmentId;
            }

            filters.limit = filters.limit || 100;

            return filters;
        },

        uploadContext: function (scope) {
            var context = this.context(scope);
            var form = scope.find('[data-wimedia-upload-form], [data-media-upload-form]').first();

            if (!form.length) {
                return context;
            }

            form.find('input, select, textarea').each(function () {
                var input = $(this);
                var name = input.attr('name');
                var value;

                if (!name || input.attr('type') === 'file') {
                    return;
                }

                if (input.attr('type') === 'checkbox') {
                    value = input.is(':checked') ? (input.val() || '1') : '';
                } else {
                    value = input.val();
                }

                if (value !== undefined && value !== null && value !== '') {
                    context[name] = value;
                }
            });

            return context;
        },

        loadAll: function (scope) {
            var self = this;

            this.setLoading(scope, true);
            this.loadList(scope, {})
                .always(function () {
                    self.setLoading(scope, false);
                });
        },

        loadType: function (scope, type) {
            if (type === 'video_audio') {
                this.switchTab(scope, 'media');
                this.loadList(scope, {});
                return;
            }

            if (type === 'image') {
                this.switchTab(scope, 'images');
            }

            if (type === 'document') {
                this.switchTab(scope, 'documents');
            }

            this.loadList(scope, {media_type: type});
        },

        loadLinked: function (scope) {
            var self = this;
            var filters = {
                system_code: scope.find('[data-linked-system-code]').val() || '',
                entity_type: scope.find('[data-linked-entity-type]').val() || '',
                entity_id: scope.find('[data-linked-entity-id]').val() || '',
                link_type: scope.find('[data-linked-link-type]').val() || ''
            };

            if (!filters.system_code || !filters.entity_type || !filters.entity_id) {
                this.message(scope, 'System, entity type and entity ID are required to load linked media.', 'warning');
                return;
            }

            this.setLoading(scope, true);

            window.WIMedia.linked(filters)
                .done(function (response) {
                    var rows = window.WIMedia.responseItems(response);

                    self.cache.linked = rows;
                    self.renderList(scope, 'linked', rows, '[data-media-linked-list], [data-wimedia-list="linked"]');
                    self.message(scope, rows.length + ' linked media item' + (rows.length === 1 ? '' : 's') + ' loaded.', 'success');
                })
                .fail(function (xhr) {
                    self.message(scope, self.errorMessage(xhr, 'Unable to load linked media.'), 'error');
                })
                .always(function () {
                    self.setLoading(scope, false);
                });
        },

        loadList: function (scope, extra) {
            var self = this;

            return window.WIMedia.list(this.filters(scope, extra || {}))
                .done(function (response) {
                    var rows = window.WIMedia.responseItems(response);

                    self.cache.all = rows;
                    self.cache.documents = self.filterRows(rows, 'document');
                    self.cache.images = self.filterRows(rows, 'image');
                    self.cache.media = rows.filter(function (item) {
                        return item.media_type === 'video' || item.media_type === 'audio';
                    });

                    self.activeItems = self.cache.media.length ? self.cache.media : rows;
                    self.activeIndex = 0;
                    self.renderAll(scope);
                    self.message(scope, rows.length + ' media item' + (rows.length === 1 ? '' : 's') + ' loaded.', 'success', true);
                })
                .fail(function (xhr) {
                    self.message(scope, self.errorMessage(xhr, 'Unable to load media.'), 'error');
                });
        },

        filterRows: function (rows, type) {
            return (rows || []).filter(function (item) {
                return item.media_type === type;
            });
        },

        renderAll: function (scope) {
            this.renderList(scope, 'all', this.cache.all, '[data-media-list], [data-wimedia-list="all"]');
            this.renderList(scope, 'documents', this.cache.documents, '[data-media-document-list], [data-wimedia-list="documents"]');
            this.renderList(scope, 'images', this.cache.images, '[data-media-image-grid], [data-wimedia-list="images"]');
            this.renderList(scope, 'media', this.cache.media, '[data-media-video-audio-list], [data-wimedia-list="media"]');

            if (this.cache.linked.length) {
                this.renderList(scope, 'linked', this.cache.linked, '[data-media-linked-list], [data-wimedia-list="linked"]');
            }

            this.renderStats(scope);
        },

        renderList: function (scope, target, rows, selector) {
            var box = scope.find(selector).first();

            if (!box.length) {
                box = scope.find('[data-wimedia-list="' + target + '"]').first();
            }

            if (!box.length) {
                return;
            }

            if (!rows || !rows.length) {
                box.html('<p class="wi-media-empty">No media found.</p>');
                return;
            }

            box.html(rows.map(this.renderCard.bind(this)).join(''));
        },

        renderCard: function (item) {
            var id = item.id || item.media_id || '';
            var title = item.title || item.original_name || item.file_name || ('Media #' + id);
            var type = item.media_type || item.file_type || 'file';
            var size = this.formatSize(item.file_size || item.size || 0);
            var extension = item.extension || item.file_extension || '';
            var status = item.processing_status || item.scan_status || '';
            var preview = this.previewMarkup(item);

            return '' +
                '<article class="wi-media-card" data-wimedia-item data-media-item data-wimedia-id="' + this.escape(id) + '" data-media-id="' + this.escape(id) + '">' +
                    '<div class="wi-media-card__preview">' + preview + '</div>' +
                    '<div class="wi-media-card__body">' +
                        '<strong class="wi-media-card__title">' + this.escape(title) + '</strong>' +
                        '<span class="wi-media-card__meta">' + this.escape(type) + (extension ? ' · ' + this.escape(extension) : '') + (size ? ' · ' + this.escape(size) : '') + '</span>' +
                        (status ? '<span class="wi-media-card__status">' + this.escape(status) + '</span>' : '') +
                    '</div>' +
                    '<div class="wi-media-card__actions">' +
                        '<button type="button" class="wi-media-btn wi-media-btn-small" data-wimedia-centre-view="' + this.escape(id) + '">View</button>' +
                        '<button type="button" class="wi-media-btn wi-media-btn-small" data-wimedia-centre-download="' + this.escape(id) + '">Download</button>' +
                    '</div>' +
                '</article>';
        },

        previewMarkup: function (item) {
            var type = item.media_type || item.file_type || '';
            var url = item.safe_view_url || item.thumbnail_url || item.thumb_url || item.file_url || item.url || '';

            if (type === 'image' && url) {
                return '<img src="' + this.escape(url) + '" alt="">';
            }

            if (type === 'video') {
                return '<span class="wi-media-file-icon">▶</span>';
            }

            if (type === 'audio') {
                return '<span class="wi-media-file-icon">♫</span>';
            }

            if (type === 'document') {
                return '<span class="wi-media-file-icon">DOC</span>';
            }

            if (type === 'archive') {
                return '<span class="wi-media-file-icon">ZIP</span>';
            }

            return '<span class="wi-media-file-icon">FILE</span>';
        },

        uploadFromForm: function (scope, form) {
            var input = $(form).find('[data-wimedia-file-input], [data-media-file], input[type="file"]').first().get(0);
            var files = input && input.files ? input.files : [];

            if (!files.length) {
                this.message(scope, 'Please choose at least one file to upload.', 'warning');
                return;
            }

            this.uploadFiles(scope, files, this.uploadContext(scope));
        },

        uploadFiles: function (scope, files, context) {
            var self = this;
            var status = scope.find('[data-wimedia-upload-progress], [data-media-upload-progress]').first();

            if (!files || !files.length) {
                this.message(scope, 'No files selected.', 'warning');
                return;
            }

            status.text('Uploading ' + files.length + ' file' + (files.length === 1 ? '' : 's') + '...');
            this.message(scope, 'Upload started.', 'info', true);
            this.setLoading(scope, true);

            window.WIMedia.uploadBulk(files, context || {})
                .done(function (response) {
                    var message = window.WIMedia.responseValue(response, 'message') || 'Upload complete.';
                    var success = window.WIMedia.isSuccess(response);

                    status.text(message);
                    self.message(scope, message, success ? 'success' : 'warning');
                    self.clearFileInput(scope);
                    self.loadAll(scope);
                })
                .fail(function (xhr) {
                    var message = self.errorMessage(xhr, 'Upload failed.');
                    status.text(message);
                    self.message(scope, message, 'error');
                })
                .always(function () {
                    self.setLoading(scope, false);
                });
        },

        updateSelectedFiles: function (scope, files) {
            var count = files ? files.length : 0;
            var target = scope.find('[data-wimedia-selected-files], [data-media-selected-files]').first();
            var totalSize = 0;
            var i;

            if (!target.length) {
                return;
            }

            if (!count) {
                target.text('No files selected.');
                return;
            }

            for (i = 0; i < files.length; i += 1) {
                totalSize += files[i].size || 0;
            }

            target.text(count + ' file' + (count === 1 ? '' : 's') + ' selected · ' + this.formatSize(totalSize));
        },

        clearFileInput: function (scope) {
            scope.find('[data-wimedia-file-input], [data-media-file], input[type="file"]').val('');
            this.updateSelectedFiles(scope, []);
        },

        selectItem: function (scope, id) {
            var item = this.findItem(id);

            if (!item) {
                return;
            }

            if (item.media_type === 'image') {
                this.previewImage(scope, item);
                this.switchTab(scope, 'images');
                return;
            }

            if (item.media_type === 'video' || item.media_type === 'audio') {
                this.playItem(scope, item);
                this.switchTab(scope, 'media');
                return;
            }

            this.openView(scope, id);
        },

        findItem: function (id) {
            var rows = this.cache.all.concat(this.cache.linked);
            var i;

            for (i = 0; i < rows.length; i += 1) {
                if (parseInt(rows[i].id || rows[i].media_id, 10) === parseInt(id, 10)) {
                    return rows[i];
                }
            }

            return null;
        },

        previewImage: function (scope, item) {
            var box = scope.find('[data-media-image-preview], [data-wimedia-image-preview]').first();
            var src = item.safe_view_url || item.thumbnail_url || item.file_url || item.url || '';

            if (!box.length || !src) {
                return;
            }

            box.html('<img src="' + this.escape(src) + '" alt="' + this.escape(item.title || item.original_name || '') + '">');
        },

        playRelative: function (scope, direction) {
            if (!this.activeItems.length) {
                return;
            }

            this.activeIndex = (this.activeIndex + direction + this.activeItems.length) % this.activeItems.length;
            this.playItem(scope, this.activeItems[this.activeIndex]);
        },

        playItem: function (scope, item) {
            var stage = scope.find('[data-wimedia-player-stage], [data-media-player]').first();
            var src = item.safe_view_url || item.file_url || item.url || '';

            if (!stage.length || !src) {
                return;
            }

            if (item.media_type === 'video') {
                stage.html('<video src="' + this.escape(src) + '" controls></video>');
                return;
            }

            if (item.media_type === 'audio') {
                stage.html('<audio src="' + this.escape(src) + '" controls></audio>');
            }
        },

        openView: function (scope, id) {
            var self = this;

            window.WIMedia.viewUrl(id, this.context(scope))
                .done(function (response) {
                    var url = window.WIMedia.responseValue(response, 'url');

                    if (url) {
                        window.open(url, '_blank', 'noopener');
                        return;
                    }

                    self.message(scope, 'View URL was not returned.', 'warning');
                })
                .fail(function (xhr) {
                    self.message(scope, self.errorMessage(xhr, 'Unable to open media.'), 'error');
                });
        },

        openDownload: function (scope, id) {
            var self = this;

            window.WIMedia.downloadUrl(id, this.context(scope))
                .done(function (response) {
                    var url = window.WIMedia.responseValue(response, 'url');

                    if (url) {
                        window.open(url, '_blank', 'noopener');
                        return;
                    }

                    self.message(scope, 'Download URL was not returned.', 'warning');
                })
                .fail(function (xhr) {
                    self.message(scope, self.errorMessage(xhr, 'Unable to download media.'), 'error');
                });
        },

        renderStats: function (scope) {
            var box = scope.find('[data-media-stats], [data-wimedia-stats]').first();
            var html = '<span>Total: ' + this.cache.all.length + '</span>' +
                '<span>Documents: ' + this.cache.documents.length + '</span>' +
                '<span>Images: ' + this.cache.images.length + '</span>' +
                '<span>Video/Audio: ' + this.cache.media.length + '</span>';

            scope.find('[data-wimedia-stat="total"]').text(this.cache.all.length);
            scope.find('[data-wimedia-stat="documents"]').text(this.cache.documents.length);
            scope.find('[data-wimedia-stat="images"]').text(this.cache.images.length);
            scope.find('[data-wimedia-stat="media"]').text(this.cache.media.length);

            if (box.length) {
                box.html(html);
            }
        },

        setLoading: function (scope, loading) {
            scope.toggleClass('is-loading', !!loading);
        },

        message: function (scope, message, type, soft) {
            var box = scope.find('[data-wimedia-message], [data-media-message]').first();

            if (!box.length) {
                if (!soft && window.console && window.console.log) {
                    window.console.log(message);
                }
                return;
            }

            box
                .removeAttr('hidden')
                .removeClass('is-success is-error is-warning is-info')
                .addClass('is-' + (type || 'info'))
                .text(message || '');
        },

        errorMessage: function (xhr, fallback) {
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }

            if (xhr && xhr.responseText) {
                return xhr.responseText.replace(/<[^>]+>/g, '').substring(0, 220);
            }

            return fallback;
        },

        formatSize: function (bytes) {
            var size = parseInt(bytes, 10) || 0;
            var units = ['B', 'KB', 'MB', 'GB'];
            var index = 0;

            while (size >= 1024 && index < units.length - 1) {
                size = size / 1024;
                index += 1;
            }

            if (index === 0) {
                return size + ' ' + units[index];
            }

            return size.toFixed(1) + ' ' + units[index];
        },

        attr: function (element, key) {
            var el = $(element);

            return el.attr('data-wimedia-' + key) || el.attr('data-media-' + key) || el.data('wimedia-' + key) || el.data('media-' + key);
        },

        escape: function (value) {
            return $('<div>').text(value === undefined || value === null ? '' : String(value)).html();
        }
    };

    window.WIMediaCenter = WIMediaCenter;
    window.WIMediaCentre = WIMediaCenter;

    $(function () {
        WIMediaCenter.init();
    });
})(window, document, window.jQuery);
