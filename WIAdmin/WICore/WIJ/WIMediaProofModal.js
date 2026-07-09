/*!
 * File Information
 * ----------------
 * Written By: WI Labs
 * Company: WI Labs
 * Product: WICMS / WICOS
 * Project: WIMedia Shared Proof System
 * File: WIMediaProofModal.js
 * Location: root/WIAdmin/WICore/WIJ/WIMediaProofModal.js
 * Type: JavaScript
 * Layer: Shared Admin Media UI
 * Purpose Area: Camera / Upload / Media Library Proof Modal
 * Version: 1.1.0
 * Created: 2026-05-14
 * Last Updated: 2026-05-14
 * Status: Production-ready first pass
 */

(function (window, document, $) {
    'use strict';

    var WIMediaProofModal = {
        state: {
            context: {},
            stream: null,
            hasCamera: false,
            busy: false
        },

        selectors: {
            trigger: '[data-wimedia-proof-trigger]',
            attachmentList: '[data-wimedia-proof-attachments]'
        },

        init: function () {
            this.bindEvents();
            this.ensureModal();
            this.refreshAttachmentLists();
        },

        bindEvents: function () {
            var self = this;

            document.addEventListener('click', function (event) {
                var trigger = event.target.closest(self.selectors.trigger);

                if (trigger) {
                    event.preventDefault();
                    self.openFromTrigger(trigger);
                    return;
                }

                if (event.target.closest('[data-wimedia-proof-close]')) {
                    event.preventDefault();
                    self.close();
                    return;
                }

                if (event.target.closest('[data-wimedia-proof-take-photo]')) {
                    event.preventDefault();
                    self.startCamera();
                    return;
                }

                if (event.target.closest('[data-wimedia-proof-capture]')) {
                    event.preventDefault();
                    self.capturePhoto();
                    return;
                }

                if (event.target.closest('[data-wimedia-proof-upload]')) {
                    event.preventDefault();
                    self.openUploadInput();
                    return;
                }

                if (event.target.closest('[data-wimedia-proof-library]')) {
                    event.preventDefault();
                    self.openLibrary();
                    return;
                }

                var preview = event.target.closest('[data-wimedia-proof-preview]');
                if (preview) {
                    event.preventDefault();
                    self.preview(preview.getAttribute('data-media-id'));
                    return;
                }

                var detach = event.target.closest('[data-wimedia-proof-detach]');
                if (detach) {
                    event.preventDefault();
                    self.detach(detach.getAttribute('data-media-id') || detach.getAttribute('data-link-id'));
                }
            });

            document.addEventListener('change', function (event) {
                if (event.target && event.target.matches('[data-wimedia-proof-file-input]')) {
                    self.uploadFiles(event.target.files);
                    event.target.value = '';
                }
            });
        },

        openFromTrigger: function (trigger) {
            this.open(this.parseContext(trigger.getAttribute('data-wimedia-context')));
        },

        open: function (context) {
            var self = this;

            this.state.context = this.normaliseContext(context || {});
            this.setStatus('Loading media options...');
            this.show();

            this.checkCamera()
                .then(function (hasCamera) {
                    self.state.hasCamera = hasCamera;
                    self.renderOptions();
                    self.loadAttached();
                })
                .catch(function () {
                    self.state.hasCamera = false;
                    self.renderOptions();
                    self.loadAttached();
                });
        },

        close: function () {
            this.stopCamera();

            var modal = document.querySelector('[data-wimedia-proof-modal]');
            if (modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }
        },

        ensureModal: function () {
            if (document.querySelector('[data-wimedia-proof-modal]')) {
                return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'wi-media-proof-modal';
            wrapper.setAttribute('data-wimedia-proof-modal', '1');
            wrapper.setAttribute('aria-hidden', 'true');

            wrapper.innerHTML = [
                '<div class="wi-media-proof-modal__backdrop" data-wimedia-proof-close="1"></div>',
                '<div class="wi-media-proof-modal__dialog" role="dialog" aria-modal="true" aria-label="Add proof">',
                    '<div class="wi-media-proof-modal__header">',
                        '<div>',
                            '<h3 class="wi-media-proof-modal__title">Add proof</h3>',
                            '<p class="wi-media-proof-modal__subtitle">Take a photo, upload from this device, or choose from WIMedia.</p>',
                        '</div>',
                        '<button type="button" class="wi-media-proof-modal__close" data-wimedia-proof-close="1" aria-label="Close">×</button>',
                    '</div>',
                    '<div class="wi-media-proof-modal__body">',
                        '<div class="wi-media-proof-modal__status" data-wimedia-proof-status></div>',
                        '<div class="wi-media-proof-modal__options" data-wimedia-proof-options></div>',
                        '<div class="wi-media-proof-modal__camera" data-wimedia-proof-camera hidden>',
                            '<video class="wi-media-proof-modal__video" data-wimedia-proof-video autoplay playsinline></video>',
                            '<canvas class="wi-media-proof-modal__canvas" data-wimedia-proof-canvas hidden></canvas>',
                            '<div class="wi-media-proof-modal__camera-actions">',
                                '<button type="button" class="wi-media-proof-modal__button wi-media-proof-modal__button--primary" data-wimedia-proof-capture="1">Capture Photo</button>',
                            '</div>',
                        '</div>',
                        '<input type="file" data-wimedia-proof-file-input multiple hidden>',
                        '<div class="wi-media-proof-modal__library" data-wimedia-proof-library-panel hidden>',
                            '<div class="wi-media-proof-modal__library-head">',
                                '<input type="search" data-wimedia-proof-library-search placeholder="Search WIMedia library">',
                                '<button type="button" data-wimedia-proof-library-refresh>Search</button>',
                            '</div>',
                            '<div data-wimedia-proof-library-results></div>',
                        '</div>',
                        '<div class="wi-media-proof-modal__attached">',
                            '<h4>Attached proof</h4>',
                            '<div data-wimedia-proof-attached-list></div>',
                        '</div>',
                    '</div>',
                '</div>'
            ].join('');

            document.body.appendChild(wrapper);

            var self = this;
            wrapper.addEventListener('click', function (event) {
                var refresh = event.target.closest('[data-wimedia-proof-library-refresh]');
                var select = event.target.closest('[data-wimedia-proof-library-select]');

                if (refresh) {
                    event.preventDefault();
                    self.loadLibrary();
                }

                if (select) {
                    event.preventDefault();
                    self.attachExisting(select.getAttribute('data-media-id'));
                }
            });
        },

        show: function () {
            this.ensureModal();

            var modal = document.querySelector('[data-wimedia-proof-modal]');
            if (modal) {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            }
        },

        renderOptions: function () {
            var holder = document.querySelector('[data-wimedia-proof-options]');
            if (!holder) {
                return;
            }

            var buttons = [];

            if (this.state.hasCamera) {
                buttons.push(
                    '<button type="button" class="wi-media-proof-option" data-wimedia-proof-take-photo="1">' +
                    '<span class="wi-media-proof-option__icon">📷</span>' +
                    '<span><strong>Take Photo</strong><small>Use camera or webcam</small></span>' +
                    '</button>'
                );
            }

            buttons.push(
                '<button type="button" class="wi-media-proof-option" data-wimedia-proof-upload="1">' +
                '<span class="wi-media-proof-option__icon">⬆</span>' +
                '<span><strong>Upload From Device</strong><small>Choose a photo, PDF, document, or video</small></span>' +
                '</button>'
            );

            buttons.push(
                '<button type="button" class="wi-media-proof-option" data-wimedia-proof-library="1">' +
                '<span class="wi-media-proof-option__icon">🗂</span>' +
                '<span><strong>Choose From WIMedia</strong><small>Select something already uploaded</small></span>' +
                '</button>'
            );

            holder.innerHTML = buttons.join('');
            this.setStatus(this.state.hasCamera ? 'Camera available.' : 'No camera detected. Upload or library selection available.');
        },

        checkCamera: function () {
            if (!navigator.mediaDevices || typeof navigator.mediaDevices.enumerateDevices !== 'function') {
                return Promise.resolve(false);
            }

            return navigator.mediaDevices.enumerateDevices()
                .then(function (devices) {
                    return devices.some(function (device) {
                        return device.kind === 'videoinput';
                    });
                })
                .catch(function () {
                    return false;
                });
        },

        startCamera: function () {
            var self = this;

            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                this.setStatus('Camera is not available on this device.');
                return;
            }

            this.stopCamera();

            navigator.mediaDevices.getUserMedia({
                video: {facingMode: 'environment'},
                audio: false
            }).then(function (stream) {
                self.state.stream = stream;

                var camera = document.querySelector('[data-wimedia-proof-camera]');
                var video = document.querySelector('[data-wimedia-proof-video]');

                if (camera) {
                    camera.hidden = false;
                }

                if (video) {
                    video.srcObject = stream;
                    video.play();
                }

                self.setStatus('Camera ready. Capture the proof photo when ready.');
            }).catch(function () {
                self.setStatus('Camera permission denied or unavailable.');
            });
        },

        stopCamera: function () {
            if (this.state.stream) {
                this.state.stream.getTracks().forEach(function (track) {
                    track.stop();
                });
            }

            this.state.stream = null;

            var camera = document.querySelector('[data-wimedia-proof-camera]');
            var video = document.querySelector('[data-wimedia-proof-video]');

            if (camera) {
                camera.hidden = true;
            }

            if (video) {
                video.srcObject = null;
            }
        },

        capturePhoto: function () {
            var video = document.querySelector('[data-wimedia-proof-video]');
            var canvas = document.querySelector('[data-wimedia-proof-canvas]');

            if (!video || !canvas) {
                this.setStatus('Camera capture area is not available.');
                return;
            }

            var width = video.videoWidth || 1280;
            var height = video.videoHeight || 720;

            canvas.width = width;
            canvas.height = height;
            canvas.getContext('2d').drawImage(video, 0, 0, width, height);

            var self = this;

            canvas.toBlob(function (blob) {
                if (!blob) {
                    self.setStatus('Photo could not be captured.');
                    return;
                }

                self.uploadFiles([
                    new File([blob], 'proof-photo-' + Date.now() + '.jpg', {type: 'image/jpeg'})
                ]);
            }, 'image/jpeg', 0.92);
        },

        openUploadInput: function () {
            var input = document.querySelector('[data-wimedia-proof-file-input]');
            if (!input) {
                return;
            }

            input.setAttribute('accept', (this.state.context.accepted_types || ['image/*', 'application/pdf']).join(','));
            input.click();
        },

        uploadFiles: function (files) {
            var self = this;

            if (!files || !files.length) {
                return;
            }

            this.setBusy(true);
            this.setStatus('Uploading proof...');

            var formData = new FormData();
            formData.append('action', 'compliance_media_upload');
            formData.append('context', JSON.stringify(this.state.context));

            Array.prototype.forEach.call(files, function (file) {
                formData.append('files[]', file);
            });

            fetch(this.ajaxUrl(), {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json();
            }).then(function (response) {
                self.handleUploadResponse(response);
            }).catch(function () {
                self.setStatus('Upload failed. Please check the request.');
            }).finally(function () {
                self.setBusy(false);
            });
        },

        handleUploadResponse: function (response) {
            if (!response || response.success !== true) {
                this.setStatus((response && response.message) ? response.message : 'Upload failed.');
                return;
            }

            this.setStatus(response.message || 'Proof uploaded.');
            this.loadAttached();
            this.refreshAttachmentLists();
        },

        openLibrary: function () {
            var panel = document.querySelector('[data-wimedia-proof-library-panel]');

            if (panel) {
                panel.hidden = !panel.hidden;
            }

            if (panel && !panel.hidden) {
                this.loadLibrary();
            }
        },

        loadLibrary: function () {
            var self = this;
            var input = document.querySelector('[data-wimedia-proof-library-search]');
            var q = input ? input.value : '';

            this.setStatus('Loading WIMedia library...');

            this.post({
                action: 'compliance_media_library_search',
                q: q,
                context: JSON.stringify(this.state.context)
            }).then(function (response) {
                var items = response && response.data && response.data.items ? response.data.items : [];
                self.renderLibrary(items);
                self.setStatus(items.length ? 'Select a media item to attach.' : 'No media items found.');
            }).catch(function () {
                self.setStatus('WIMedia library could not be loaded.');
            });
        },

        renderLibrary: function (items) {
            var holder = document.querySelector('[data-wimedia-proof-library-results]');
            if (!holder) {
                return;
            }

            if (!items || !items.length) {
                holder.innerHTML = '<p class="wi-media-proof-empty">No media found.</p>';
                return;
            }

            holder.innerHTML = items.map(function (item) {
                var mediaId = item.id || item.media_id || item.mediaId || '';
                var title = item.title || item.filename || item.original_name || ('Media #' + mediaId);

                return [
                    '<div class="wi-media-proof-library-item">',
                        '<div>',
                            '<strong>' + WIMediaProofModal.escape(title) + '</strong>',
                            '<small>Media ID: ' + WIMediaProofModal.escape(String(mediaId)) + '</small>',
                        '</div>',
                        '<button type="button" data-wimedia-proof-library-select="1" data-media-id="' + WIMediaProofModal.escape(String(mediaId)) + '">Attach</button>',
                    '</div>'
                ].join('');
            }).join('');
        },

        attachExisting: function (mediaId) {
            var self = this;
            mediaId = parseInt(mediaId || 0, 10);

            if (!mediaId) {
                this.setStatus('Invalid media selected.');
                return;
            }

            this.setBusy(true);

            this.post({
                action: 'compliance_media_proof_attach',
                media_id: mediaId,
                context: JSON.stringify(this.state.context)
            }).then(function (response) {
                self.setStatus(response.message || 'Media attached.');
                self.loadAttached();
                self.refreshAttachmentLists();
            }).catch(function () {
                self.setStatus('Media could not be attached.');
            }).finally(function () {
                self.setBusy(false);
            });
        },

        loadAttached: function () {
            var self = this;

            this.post({
                action: 'compliance_media_proof_list',
                context: JSON.stringify(this.state.context)
            }).then(function (response) {
                var items = response && response.data && response.data.items ? response.data.items : [];
                self.renderAttached(items);
            }).catch(function () {
                self.renderAttached([]);
            });
        },

        detach: function (mediaId) {
            var self = this;
            mediaId = parseInt(mediaId || 0, 10);

            if (!mediaId) {
                this.setStatus('Missing media id.');
                return;
            }

            this.post({
                action: 'compliance_media_proof_detach',
                media_id: mediaId,
                context: JSON.stringify(this.state.context)
            }).then(function (response) {
                self.setStatus(response.message || 'Media detached.');
                self.loadAttached();
                self.refreshAttachmentLists();
            }).catch(function () {
                self.setStatus('Media could not be detached.');
            });
        },

        preview: function (mediaId) {
            mediaId = parseInt(mediaId || 0, 10);

            if (!mediaId) {
                return;
            }

            this.post({
                action: 'compliance_media_preview',
                media_id: mediaId,
                context: JSON.stringify(this.state.context)
            }).then(function (response) {
                var url = response && response.data ? (response.data.url || response.data.preview_url) : '';

                if (url) {
                    window.open(url, '_blank', 'noopener');
                }
            });
        },

        renderAttached: function (items) {
            var holder = document.querySelector('[data-wimedia-proof-attached-list]');
            if (!holder) {
                return;
            }

            if (!items || !items.length) {
                holder.innerHTML = '<p class="wi-media-proof-empty">No proof attached yet.</p>';
                return;
            }

            holder.innerHTML = items.map(function (item) {
                var mediaId = item.media_id || item.id || '';
                var title = item.title || item.filename || item.original_name || ('Media #' + mediaId);

                return [
                    '<div class="wi-media-proof-attached-item">',
                        '<div>',
                            '<strong>' + WIMediaProofModal.escape(title) + '</strong>',
                            '<small>Media ID: ' + WIMediaProofModal.escape(String(mediaId)) + '</small>',
                        '</div>',
                        '<div class="wi-media-proof-attached-actions">',
                            '<button type="button" data-wimedia-proof-preview="1" data-media-id="' + WIMediaProofModal.escape(String(mediaId)) + '">Preview</button>',
                            '<button type="button" data-wimedia-proof-detach="1" data-media-id="' + WIMediaProofModal.escape(String(mediaId)) + '">Remove</button>',
                        '</div>',
                    '</div>'
                ].join('');
            }).join('');
        },

        refreshAttachmentLists: function () {
            var self = this;
            var lists = document.querySelectorAll(this.selectors.attachmentList);

            Array.prototype.forEach.call(lists, function (list) {
                var context = self.parseContext(list.getAttribute('data-wimedia-context'));

                self.post({
                    action: 'compliance_media_proof_list',
                    context: JSON.stringify(context)
                }).then(function (response) {
                    var items = response && response.data && response.data.items ? response.data.items : [];

                    list.innerHTML = items.length
                        ? '<span class="wi-media-proof-count">' + items.length + ' proof file(s) attached</span>'
                        : '<span class="wi-media-proof-count">No proof attached</span>';
                }).catch(function () {
                    list.innerHTML = '';
                });
            });
        },

        post: function (payload) {
            var body = new URLSearchParams();

            Object.keys(payload).forEach(function (key) {
                body.append(key, payload[key]);
            });

            return fetch(this.ajaxUrl(), {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                body: body.toString(),
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json();
            });
        },

        ajaxUrl: function () {
            if (window.WIMediaProofConfig && window.WIMediaProofConfig.ajaxUrl) {
                return window.WIMediaProofConfig.ajaxUrl;
            }

            return 'WICore/WIClass/WIAjaxCompliance.php';
        },

        setBusy: function (busy) {
            this.state.busy = !!busy;

            var modal = document.querySelector('[data-wimedia-proof-modal]');
            if (modal) {
                modal.classList.toggle('is-busy', this.state.busy);
            }
        },

        setStatus: function (message) {
            var status = document.querySelector('[data-wimedia-proof-status]');
            if (status) {
                status.textContent = message || '';
            }
        },

        parseContext: function (raw) {
            if (!raw) {
                return {};
            }

            try {
                return JSON.parse(raw);
            } catch (e) {
                return {};
            }
        },

        normaliseContext: function (context) {
            context = context || {};

            return {
                system_code: context.system_code || 'wicos',
                entity_type: context.entity_type || context.entityType || 'generic',
                entity_id: parseInt(context.entity_id || context.entityId || 0, 10),
                purpose: context.purpose || context.link_type || 'proof',
                link_type: context.link_type || context.purpose || 'proof',

                business_id: parseInt(context.business_id || context.businessId || context.org_business_id || 0, 10),
                site_id: parseInt(context.site_id || context.siteId || context.org_site_id || 0, 10),
                department_id: parseInt(context.department_id || context.departmentId || context.org_department_id || 0, 10),

                org_business_id: parseInt(context.org_business_id || context.business_id || context.businessId || 0, 10),
                org_site_id: parseInt(context.org_site_id || context.site_id || context.siteId || 0, 10),
                org_department_id: parseInt(context.org_department_id || context.department_id || context.departmentId || 0, 10),

                required: !!context.required,
                multiple: context.multiple !== false,
                accepted_types: context.accepted_types || context.acceptedTypes || ['image/*', 'application/pdf'],
                label: context.label || 'Add proof',
                source: context.source || 'wimedia_proof_modal',
                visibility: context.visibility || 'private',
                access_scope: context.access_scope || 'business',
                is_private: context.is_private !== undefined ? context.is_private : 1,
                is_sensitive: context.is_sensitive !== undefined ? context.is_sensitive : 1,
                metadata: context.metadata || {}
            };
        },

        escape: function (value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    };

    window.WIMediaProofModal = WIMediaProofModal;

    window.WIMediaProof = {
        open: function (context) {
            WIMediaProofModal.open(context || {});
        },
        refresh: function () {
            WIMediaProofModal.refreshAttachmentLists();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            WIMediaProofModal.init();
        });
    } else {
        WIMediaProofModal.init();
    }

})(window, document, window.jQuery);