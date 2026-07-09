/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: WIMediaContract.js
| Location: /root/WIAdmin/WICore/WIJ/WIMediaContract.js
| Type: JavaScript Adapter
| Layer: Shared Admin Media Contract
| Purpose Area: WIMedia Hook/API Compatibility Layer
| Version: 1.0.0
| Created: 2026-05-11
| Last Updated: 2026-05-11
| Status: WM-02 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Provides a stable frontend API for WIMedia hooks:
| - WIMedia.upload(file, context)
| - WIMedia.uploadBulk(files, context)
| - WIMedia.list(filters)
| - WIMedia.attach(mediaId, context)
| - WIMedia.detach(mediaId, context)
| - WIMedia.linked(context)
| - WIMedia.openView(mediaId, context)
| - WIMedia.openDownload(mediaId, context)
|
| This file does not replace WIMedia.js. It patches missing methods only.
|--------------------------------------------------------------------------
*/

(function (window, document, $) {
    'use strict';

    if (!$) {
        if (window.console && window.console.error) {
            window.console.error('WIMediaContract.js requires jQuery.');
        }
        return;
    }

    window.WIMedia = window.WIMedia || {};

    var Contract = {
        defaultEndpoint: 'WIAjax.php',

        init: function () {
            this.patch();
        },

        patch: function () {
            var media = window.WIMedia;

            if (typeof media.getEndpoint !== 'function') {
                media.getEndpoint = this.getEndpoint.bind(this);
            }

            if (typeof media.post !== 'function') {
                media.post = this.post.bind(this);
            }

            if (typeof media.upload !== 'function') {
                media.upload = this.upload.bind(this);
            }

            if (typeof media.uploadBulk !== 'function') {
                media.uploadBulk = this.uploadBulk.bind(this);
            }

            if (typeof media.list !== 'function') {
                media.list = this.list.bind(this);
            }

            if (typeof media.attach !== 'function') {
                media.attach = this.attach.bind(this);
            }

            if (typeof media.detach !== 'function') {
                media.detach = this.detach.bind(this);
            }

            if (typeof media.linked !== 'function') {
                media.linked = this.linked.bind(this);
            }

            if (typeof media.openView !== 'function') {
                media.openView = this.openView.bind(this);
            }

            if (typeof media.openDownload !== 'function') {
                media.openDownload = this.openDownload.bind(this);
            }
        },

        getEndpoint: function () {
            var endpoint = '';

            if (window.WICompliance && typeof window.WICompliance.getApiUrl === 'function') {
                endpoint = window.WICompliance.getApiUrl();
            }

            if (!endpoint && window.WICore && typeof window.WICore.getApiUrl === 'function') {
                endpoint = window.WICore.getApiUrl();
            }

            if (!endpoint) {
                endpoint = $('meta[name="wi-ajax-url"]').attr('content') || '';
            }

            if (!endpoint) {
                endpoint = $('[data-wi-ajax-url]').first().attr('data-wi-ajax-url') || '';
            }

            return endpoint || this.defaultEndpoint;
        },

        csrf: function () {
            return $('[name="csrf_token"]').val() ||
                $('[name="token"]').val() ||
                $('[name="wi_csrf"]').val() ||
                $('[name="wi_ajax_token"]').val() ||
                $('[name="_token"]').val() ||
                '';
        },

        post: function (action, payload) {
            payload = payload || {};
            payload.action = action;

            var token = this.csrf();

            if (token && !payload.csrf_token) {
                payload.csrf_token = token;
            }

            return $.ajax({
                url: window.WIMedia.getEndpoint(),
                method: 'POST',
                dataType: 'json',
                data: payload
            });
        },

        upload: function (file, context) {
            var formData = new FormData();
            var token = this.csrf();

            context = context || {};

            formData.append('action', 'media_upload');
            formData.append('file', file);

            if (token) {
                formData.append('csrf_token', token);
            }

            this.appendContext(formData, context);

            return $.ajax({
                url: window.WIMedia.getEndpoint(),
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false
            });
        },

        uploadBulk: function (files, context) {
            var formData = new FormData();
            var token = this.csrf();
            var list = this.normaliseFiles(files);
            var i;

            context = context || {};

            formData.append('action', 'media_upload_bulk');

            if (token) {
                formData.append('csrf_token', token);
            }

            for (i = 0; i < list.length; i += 1) {
                formData.append('files[]', list[i]);
            }

            this.appendContext(formData, context);

            return $.ajax({
                url: window.WIMedia.getEndpoint(),
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false
            });
        },

        list: function (filters) {
            return window.WIMedia.post('media_list', filters || {});
        },

        attach: function (mediaId, context) {
            context = $.extend({}, context || {}, {
                media_id: parseInt(mediaId || 0, 10) || 0
            });

            return window.WIMedia.post('media_link', context);
        },

        detach: function (mediaId, context) {
            context = $.extend({}, context || {}, {
                media_id: parseInt(mediaId || 0, 10) || 0
            });

            return window.WIMedia.post('media_unlink', context);
        },

        linked: function (context) {
            return window.WIMedia.post('media_linked', context || {});
        },

        openView: function (mediaId, context) {
            var payload = $.extend({}, context || {}, {
                media_id: parseInt(mediaId || 0, 10) || 0
            });

            window.WIMedia.post('media_view', payload).done(function (response) {
                var url = '';

                if (response && response.data) {
                    url = response.data.view_url || response.data.url || response.data.file_url || '';
                }

                if (url) {
                    window.open(url, '_blank', 'noopener');
                    return;
                }

                if (window.console && window.console.warn) {
                    window.console.warn('No media view URL returned.', response);
                }
            });
        },

        openDownload: function (mediaId, context) {
            var endpoint = window.WIMedia.getEndpoint();
            var query = $.param($.extend({}, context || {}, {
                action: 'media_download',
                media_id: parseInt(mediaId || 0, 10) || 0,
                csrf_token: this.csrf()
            }));

            window.open(endpoint + (endpoint.indexOf('?') === -1 ? '?' : '&') + query, '_blank', 'noopener');
        },

        appendContext: function (formData, context) {
            var key;

            for (key in context) {
                if (!Object.prototype.hasOwnProperty.call(context, key)) {
                    continue;
                }

                if (context[key] === null || typeof context[key] === 'undefined') {
                    continue;
                }

                formData.append(key, context[key]);
            }
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
        }
    };

    Contract.init();

    window.WIMediaContract = Contract;
})(window, document, window.jQuery);