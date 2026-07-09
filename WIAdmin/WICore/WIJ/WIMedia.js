/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: WIMedia.js
| Location: /root/WIAdmin/WICore/WIJ/WIMedia.js
| Type: JavaScript Helper
| Layer: Shared Admin UI Support
| Purpose Area: Shared Media AJAX Foundation
| Version: 1.2.0
| Created: 2026-05-04
| Last Updated: 2026-05-08
| Status: WM-02 Production Refactor
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia AJAX helper for site-wide media operations.
| - Provides one reusable AJAX interface for upload, list, get, update, delete and restore
| - Supports link/unlink/linked lookup plus attach/detach aliases for documents/evidence/users
| - Supports view/download URL helpers
| - Contains no page-specific, compliance-specific or database logic
|--------------------------------------------------------------------------
*/

(function (window, document, $) {
    'use strict';

    if (!$) {
        if (window.console && window.console.error) {
            window.console.error('WIMedia.js requires jQuery.');
        }
        return;
    }

    var WIMedia = {
        endpoint: window.WI_MEDIA_ENDPOINT || window.WI_AJAX_URL || 'WICore/WIAjax/WIMedia.php',

        actions: {
            upload: 'media_upload',
            uploadBulk: 'media_upload_bulk',
            list: 'media_list',
            get: 'media_get',
            update: 'media_update',
            delete: 'media_delete',
            restore: 'media_restore',
            link: 'media_link',
            unlink: 'media_unlink',
            linked: 'media_linked',
            viewUrl: 'media_view_url',
            downloadUrl: 'media_download_url',
            accessCheck: 'media_access_check'
        },

        aliases: {
            'media.upload': 'media_upload',
            'media.uploadBulk': 'media_upload_bulk',
            'media.list': 'media_list',
            'media.get': 'media_get',
            'media.update': 'media_update',
            'media.delete': 'media_delete',
            'media.restore': 'media_restore',
            'media.link': 'media_link',
            'media.unlink': 'media_unlink',
            'media.attach': 'media_link',
            'media.detach': 'media_unlink',
            'media.linked': 'media_linked',
            'media.viewUrl': 'media_view_url',
            'media.downloadUrl': 'media_download_url',
            'media.accessCheck': 'media_access_check'
        },

        init: function () {
            this.bindGlobalEvents();
            this.mountAll(document);
        },

        setEndpoint: function (endpoint) {
            if (typeof endpoint === 'string' && endpoint.length > 0) {
                this.endpoint = endpoint;
            }

            return this;
        },

        resolveAction: function (action) {
            action = String(action || '').trim();
            return this.aliases[action] || action;
        },

        request: function (action, payload, options) {
            var resolvedAction = this.resolveAction(action);
            var ajaxOptions = options || {};
            var data = payload || {};
            var isFormData = (typeof FormData !== 'undefined' && data instanceof FormData);

            if (isFormData) {
                if (!data.has('action')) {
                    data.append('action', resolvedAction);
                }
            } else {
                data = $.extend({}, data, {
                    action: resolvedAction
                });
            }

            return $.ajax($.extend({
                url: ajaxOptions.endpoint || this.endpoint,
                method: ajaxOptions.method || 'POST',
                data: data,
                dataType: ajaxOptions.dataType || 'json',
                processData: !isFormData,
                contentType: isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8'
            }, ajaxOptions));
        },

        upload: function (source, context) {
            return this.request(this.actions.upload, this.buildUploadFormData(source, context || {}, false));
        },

        uploadBulk: function (source, context) {
            return this.request(this.actions.uploadBulk, this.buildUploadFormData(source, context || {}, true));
        },

        uploadAndAttach: function (source, context) {
            return this.uploadBulk(source, context || {});
        },

        list: function (filters) {
            return this.request(this.actions.list, filters || {});
        },

        get: function (mediaId, context) {
            return this.request(this.actions.get, $.extend({}, context || {}, {media_id: mediaId}));
        },

        update: function (mediaId, payload) {
            return this.request(this.actions.update, $.extend({}, payload || {}, {media_id: mediaId}));
        },

        delete: function (mediaId, context) {
            return this.request(this.actions.delete, $.extend({}, context || {}, {media_id: mediaId}));
        },

        restore: function (mediaId, context) {
            return this.request(this.actions.restore, $.extend({}, context || {}, {media_id: mediaId}));
        },

        link: function (mediaId, context) {
            return this.request(this.actions.link, $.extend({}, context || {}, {media_id: mediaId}));
        },

        attach: function (mediaId, context) {
            return this.link(mediaId, context || {});
        },

        unlink: function (mediaId, context) {
            return this.request(this.actions.unlink, $.extend({}, context || {}, {media_id: mediaId}));
        },

        detach: function (mediaId, context) {
            return this.unlink(mediaId, context || {});
        },

        linked: function (context) {
            return this.request(this.actions.linked, context || {});
        },

        viewUrl: function (mediaId, context) {
            return this.request(this.actions.viewUrl, $.extend({}, context || {}, {media_id: mediaId}));
        },

        downloadUrl: function (mediaId, context) {
            return this.request(this.actions.downloadUrl, $.extend({}, context || {}, {media_id: mediaId}));
        },

        accessCheck: function (mediaId, context) {
            return this.request(this.actions.accessCheck, $.extend({}, context || {}, {media_id: mediaId}));
        },

        openView: function (mediaId, context) {
            return this.viewUrl(mediaId, context || {}).done(function (response) {
                var url = WIMedia.responseValue(response, 'url');

                if (url) {
                    window.open(url, '_blank', 'noopener');
                }
            });
        },

        openDownload: function (mediaId, context) {
            return this.downloadUrl(mediaId, context || {}).done(function (response) {
                var url = WIMedia.responseValue(response, 'url');

                if (url) {
                    window.open(url, '_blank', 'noopener');
                }
            });
        },

        buildUploadFormData: function (source, context, bulk) {
            var formData = (typeof FormData !== 'undefined' && source instanceof FormData) ? source : new FormData();
            var files;
            var i;

            if (!(typeof FormData !== 'undefined' && source instanceof FormData)) {
                files = this.normaliseFiles(source);

                for (i = 0; i < files.length; i += 1) {
                    formData.append(bulk ? 'files[]' : 'file', files[i]);

                    if (!bulk) {
                        break;
                    }
                }
            }

            this.appendContext(formData, context || {});

            return formData;
        },

        normaliseFiles: function (source) {
            var raw = source;
            var files = [];
            var i;

            if (source && source.jquery) {
                raw = source.get(0);
            }

            if (raw && raw.files) {
                raw = raw.files;
            }

            if (typeof File !== 'undefined' && raw instanceof File) {
                return [raw];
            }

            if (raw && typeof raw.length === 'number') {
                for (i = 0; i < raw.length; i += 1) {
                    if (raw[i]) {
                        files.push(raw[i]);
                    }
                }
            }

            return files;
        },

        appendContext: function (formData, context) {
            $.each(context || {}, function (key, value) {
                if (value !== undefined && value !== null && value !== '') {
                    formData.append(key, value);
                }
            });

            return formData;
        },

        responseItems: function (response) {
            if (!response) {
                return [];
            }

            if ($.isArray(response.items)) {
                return response.items;
            }

            if ($.isArray(response.media)) {
                return response.media;
            }

            if ($.isArray(response.data)) {
                return response.data;
            }

            if (response.data && $.isArray(response.data.items)) {
                return response.data.items;
            }

            if (response.data && $.isArray(response.data.media)) {
                return response.data.media;
            }

            if (response.data && $.isArray(response.data.data)) {
                return response.data.data;
            }

            return [];
        },

        responseValue: function (response, key) {
            if (!response) {
                return null;
            }

            if (response[key] !== undefined) {
                return response[key];
            }

            if (response.data && response.data[key] !== undefined) {
                return response.data[key];
            }

            return null;
        },

        isSuccess: function (response) {
            return !!(response && (response.success === true || response.status === 'success'));
        },

        escapeHtml: function (value) {
            return $('<div>').text(value === undefined || value === null ? '' : String(value)).html();
        },

        readJsonAttribute: function (element, attribute, fallback) {
            var value = $(element).attr(attribute);

            if (!value) {
                return fallback || {};
            }

            try {
                return JSON.parse(value);
            } catch (e) {
                return fallback || {};
            }
        },

        mountAll: function (scope) {
            var self = this;
            var root = scope ? $(scope) : $(document);

            root.find('[data-wimedia-mount="upload"]').each(function () {
                self.mountUpload(this);
            });

            root.find('[data-wimedia-mount="attached-list"]').each(function () {
                self.mountAttachedList(this);
            });
        },

        mountUpload: function (target, options) {
            var self = this;
            var box = $(target);
            var context = $.extend({}, options || {}, this.readJsonAttribute(box, 'data-wimedia-context', {}));

            if (!box.length || box.data('wimedia-upload-mounted')) {
                return;
            }

            box.data('wimedia-upload-mounted', true);

            box.on('change.wimediaUploadMount', '[data-wimedia-upload-input]', function () {
                var input = this;
                var bulk = $(input).prop('multiple') === true;
                var status = box.find('[data-wimedia-upload-status]');

                status.text('Uploading...');

                (bulk ? self.uploadBulk(input, context) : self.upload(input, context))
                    .done(function (response) {
                        status.text(response.message || 'Upload complete.');
                        box.trigger('wimedia:uploaded', [response]);
                    })
                    .fail(function () {
                        status.text('Upload failed.');
                    });
            });
        },

        mountAttachedList: function (target, options) {
            var self = this;
            var box = $(target);
            var context = $.extend({}, options || {}, this.readJsonAttribute(box, 'data-wimedia-context', {}));

            if (!box.length) {
                return;
            }

            self.linked(context).done(function (response) {
                var rows = self.responseItems(response);

                if (!rows.length) {
                    box.html('<p class="wi-media-empty">No media attached.</p>');
                    return;
                }

                box.html(rows.map(function (item) {
                    var id = item.id || item.media_id || '';
                    var title = item.title || item.original_name || item.file_name || ('Media #' + id);

                    return '<article class="wi-media-attached-card" data-wimedia-attached-id="' + self.escapeHtml(id) + '">' +
                        '<strong>' + self.escapeHtml(title) + '</strong>' +
                        '<span>' + self.escapeHtml(item.media_type || 'file') + '</span>' +
                        '<button type="button" data-wimedia-view="' + self.escapeHtml(id) + '">View</button>' +
                        '<button type="button" data-wimedia-download="' + self.escapeHtml(id) + '">Download</button>' +
                    '</article>';
                }).join(''));
            });
        },

        bindGlobalEvents: function () {
            var self = this;

            $(document)
                .off('click.wimediaGlobalView')
                .on('click.wimediaGlobalView', '[data-wimedia-view]', function (event) {
                    event.preventDefault();
                    self.openView($(this).attr('data-wimedia-view'), self.readJsonAttribute(this, 'data-wimedia-context', {}));
                });

            $(document)
                .off('click.wimediaGlobalDownload')
                .on('click.wimediaGlobalDownload', '[data-wimedia-download]', function (event) {
                    event.preventDefault();
                    self.openDownload($(this).attr('data-wimedia-download'), self.readJsonAttribute(this, 'data-wimedia-context', {}));
                });
        }
    };

    window.WIMedia = WIMedia;

    $(function () {
        window.WIMedia.init();
    });
})(window, document, window.jQuery);
