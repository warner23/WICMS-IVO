/**
 * WIMembers.js
 * Member workspace AJAX forms, stale-CSRF repair, action buttons and contrast toggle.
 */
(function (window, document) {
  'use strict';

  const WIMembers = {
    ajaxUrl: window.WIMEMBERS_AJAX_URL || 'WICore/WIAjax/WIMembersAjax.php',

    init() {
      this.bindAjaxForms();
      this.bindMemberActions();
      this.bindThemeToggle();
      this.bindProfilePhoto();
      this.decorateIcons();
    },

    csrf(form) {
      const scoped = form ? form.querySelector('input[name="csrf_token"]') : null;
      const page = scoped || document.querySelector('input[name="csrf_token"]');
      return (page && page.value) || window.WICSRF_TOKEN || '';
    },

    updateCsrf(token) {
      if (!token) return;
      window.WICSRF_TOKEN = token;
      document.querySelectorAll('input[name="csrf_token"]').forEach((input) => { input.value = token; });
    },

    bindAjaxForms() {
      document.querySelectorAll('[data-wi-ajax-form]').forEach((form) => {
        if (form.dataset.wiBound === '1') return;
        form.dataset.wiBound = '1';
        form.addEventListener('submit', (event) => {
          event.preventDefault();
          this.submitForm(form, false);
        });
      });
    },

    submitForm(form, retried) {
      const action = form.getAttribute('data-wi-ajax-form');
      const message = form.querySelector('[data-wi-form-message]');
      const data = new FormData(form);
      data.set('action', action);

      if (!data.get('csrf_token')) {
        data.set('csrf_token', this.csrf(form));
      }

      if (message) {
        message.textContent = 'Saving...';
        message.className = 'wi-form-message is-working';
      }

      fetch(this.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then((response) => response.json())
        .then((payload) => {
          if (payload.csrf_token) {
            this.updateCsrf(payload.csrf_token);
          }

          if (!payload.success && payload.csrf_token && !retried) {
            this.submitForm(form, true);
            return;
          }

          if (message) {
            message.textContent = payload.message || (payload.success ? 'Saved.' : 'Something went wrong.');
            message.className = 'wi-form-message ' + (payload.success ? 'is-success' : 'is-error');
          }

          if (payload.success && action === 'member_delete_request') {
            window.setTimeout(() => { window.location.href = '../login.php'; }, 1200);
          }
        })
        .catch(() => {
          if (message) {
            message.textContent = 'Request failed. Please check the network response.';
            message.className = 'wi-form-message is-error';
          }
        });
    },

    bindMemberActions() {
      document.querySelectorAll('[data-wi-member-action]').forEach((button) => {
        if (button.dataset.wiBound === '1') return;
        button.dataset.wiBound = '1';
        button.addEventListener('click', () => this.runMemberAction(button, false));
      });
    },

    runMemberAction(button, retried) {
      const action = button.getAttribute('data-wi-member-action');
      const data = new FormData();
      data.set('action', action);
      data.set('csrf_token', this.csrf(button.closest('.wi-member-shell') || document));

      if (button.dataset.assignmentId) data.set('assignment_id', button.dataset.assignmentId);
      if (button.dataset.documentId) data.set('document_id', button.dataset.documentId);
      if (button.dataset.taskId) data.set('task_id', button.dataset.taskId);

      const oldText = button.dataset.oldText || button.textContent;
      button.dataset.oldText = oldText;
      button.disabled = true;
      button.textContent = 'Saving...';

      fetch(this.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then((response) => response.json())
        .then((payload) => {
          if (payload.csrf_token) this.updateCsrf(payload.csrf_token);
          if (!payload.success && payload.csrf_token && !retried) {
            this.runMemberAction(button, true);
            return;
          }

          button.textContent = payload.message || (payload.success ? 'Saved' : 'Error');
          if (payload.success) {
            window.setTimeout(() => window.location.reload(), 500);
            return;
          }
          window.setTimeout(() => { button.disabled = false; button.textContent = oldText; }, 1400);
        })
        .catch(() => {
          button.textContent = 'Request failed';
          window.setTimeout(() => { button.disabled = false; button.textContent = oldText; }, 1400);
        });
    },



    bindProfilePhoto() {
      document.querySelectorAll('[data-wi-profile-photo-panel]').forEach((panel) => {
        if (panel.dataset.wiPhotoBound === '1') return;
        panel.dataset.wiPhotoBound = '1';
        const fileInput = panel.querySelector('[data-wi-profile-photo-file]');

        panel.querySelectorAll('[data-wi-profile-photo]').forEach((button) => {
          button.addEventListener('click', () => {
            const action = button.getAttribute('data-wi-profile-photo');
            if (action === 'upload') {
              if (fileInput) fileInput.click();
              return;
            }
            if (action === 'library') {
              this.openProfilePhotoLibrary(panel);
              return;
            }
            if (action === 'camera') {
              this.openProfilePhotoCamera(panel);
              return;
            }
            if (action === 'preview') {
              this.previewProfilePhoto(panel);
              return;
            }
            if (action === 'remove') {
              this.removeProfilePhoto(panel);
            }
          });
        });

        if (fileInput) {
          fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (file) this.uploadProfilePhoto(panel, file);
            fileInput.value = '';
          });
        }
      });
    },

    profilePhotoMessage(panel, text, state) {
      const message = panel.querySelector('[data-wi-profile-photo-message]');
      if (!message) return;
      message.textContent = text || '';
      message.className = 'wi-form-message ' + (state ? 'is-' + state : '');
    },

    updateProfilePhotoAvatar(url) {
      if (!url) return;
      document.querySelectorAll('[data-wi-profile-avatar], .wi-profile-hero__avatar img').forEach((img) => {
        img.setAttribute('src', url);
      });
    },

    uploadProfilePhoto(panel, file, retried = false) {
      if (!file || !/^image\//i.test(file.type || '')) {
        this.profilePhotoMessage(panel, 'Please choose an image file.', 'error');
        return;
      }

      const data = new FormData();
      data.set('action', 'member_profile_photo_upload');
      data.set('csrf_token', this.csrf(panel));
      data.set('profile_photo', file, file.name || 'profile-photo.jpg');

      this.profilePhotoMessage(panel, 'Uploading through WIMedia...', 'working');
      fetch(this.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then((response) => response.json())
        .then((payload) => {
          if (payload.csrf_token) this.updateCsrf(payload.csrf_token);
          if (!payload.success && payload.csrf_token && !retried) {
            this.profilePhotoMessage(panel, 'Security token refreshed. Uploading again...', 'working');
            this.uploadProfilePhoto(panel, file, true);
            return;
          }
          if (payload.success && payload.avatar_url) this.updateProfilePhotoAvatar(payload.avatar_url);
          this.profilePhotoMessage(panel, payload.message || (payload.success ? 'Profile picture updated.' : 'Upload failed.'), payload.success ? 'success' : 'error');
        })
        .catch(() => this.profilePhotoMessage(panel, 'Profile picture upload failed.', 'error'));
    },

    openProfilePhotoLibrary(panel, retried = false) {
      const modal = this.profilePhotoModal('Choose from WIMedia', '<div class="wi-profile-photo-library-status">Loading WIMedia images...</div>');
      const data = new FormData();
      data.set('action', 'member_profile_photo_library');
      data.set('csrf_token', this.csrf(panel));

      fetch(this.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then((response) => response.json())
        .then((payload) => {
          if (payload.csrf_token) this.updateCsrf(payload.csrf_token);
          if (!payload.success && payload.csrf_token && !retried) {
            this.closeProfilePhotoModal(modal);
            this.openProfilePhotoLibrary(panel, true);
            return;
          }
          const items = Array.isArray(payload.items) ? payload.items : [];
          const body = modal.querySelector('[data-wi-profile-photo-modal-body]');
          if (!body) return;
          if (!payload.success) {
            body.innerHTML = '<p class="wi-profile-photo-empty is-error">' + this.escape(payload.message || 'Could not load WIMedia library.') + '</p>';
            return;
          }
          if (!items.length) {
            body.innerHTML = '<p class="wi-profile-photo-empty">No WIMedia images found yet.</p>';
            return;
          }
          body.innerHTML = '<div class="wi-profile-photo-library">' + items.map((item) => {
            const id = parseInt(item.id || item.media_id || 0, 10);
            const src = item.safe_view_url || item.file_url || item.file_path || '';
            const title = item.title || item.original_name || ('Media #' + id);
            return '<button type="button" data-wi-profile-photo-select="' + this.escape(String(id)) + '">' +
              '<img src="' + this.escape(src) + '" alt="">' +
              '<span>' + this.escape(title) + '</span>' +
              '</button>';
          }).join('') + '</div>';
          body.querySelectorAll('[data-wi-profile-photo-select]').forEach((button) => {
            button.addEventListener('click', () => {
              const mediaId = parseInt(button.getAttribute('data-wi-profile-photo-select') || '0', 10);
              this.selectProfilePhoto(panel, mediaId, modal);
            });
          });
        })
        .catch(() => {
          const body = modal.querySelector('[data-wi-profile-photo-modal-body]');
          if (body) body.innerHTML = '<p class="wi-profile-photo-empty is-error">Could not load WIMedia library.</p>';
        });
    },

    selectProfilePhoto(panel, mediaId, modal, retried = false) {
      if (!mediaId) return;
      const data = new FormData();
      data.set('action', 'member_profile_photo_select');
      data.set('csrf_token', this.csrf(panel));
      data.set('media_id', String(mediaId));
      this.profilePhotoMessage(panel, 'Applying WIMedia image...', 'working');
      fetch(this.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then((response) => response.json())
        .then((payload) => {
          if (payload.csrf_token) this.updateCsrf(payload.csrf_token);
          if (!payload.success && payload.csrf_token && !retried) {
            this.profilePhotoMessage(panel, 'Security token refreshed. Applying image again...', 'working');
            this.selectProfilePhoto(panel, mediaId, modal, true);
            return;
          }
          if (payload.success && payload.avatar_url) {
            this.updateProfilePhotoAvatar(payload.avatar_url);
            this.closeProfilePhotoModal(modal);
          }
          this.profilePhotoMessage(panel, payload.message || (payload.success ? 'Profile picture updated.' : 'Could not apply image.'), payload.success ? 'success' : 'error');
        })
        .catch(() => this.profilePhotoMessage(panel, 'Could not apply WIMedia image.', 'error'));
    },

    removeProfilePhoto(panel, retried = false) {
      const data = new FormData();
      data.set('action', 'member_profile_photo_remove');
      data.set('csrf_token', this.csrf(panel));
      this.profilePhotoMessage(panel, 'Removing profile picture...', 'working');
      fetch(this.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: data })
        .then((response) => response.json())
        .then((payload) => {
          if (payload.csrf_token) this.updateCsrf(payload.csrf_token);
          if (!payload.success && payload.csrf_token && !retried) {
            this.profilePhotoMessage(panel, 'Security token refreshed. Removing image again...', 'working');
            this.removeProfilePhoto(panel, true);
            return;
          }
          if (payload.success && payload.avatar_url) this.updateProfilePhotoAvatar(payload.avatar_url);
          this.profilePhotoMessage(panel, payload.message || (payload.success ? 'Profile picture removed.' : 'Could not remove image.'), payload.success ? 'success' : 'error');
        })
        .catch(() => this.profilePhotoMessage(panel, 'Could not remove profile picture.', 'error'));
    },

    previewProfilePhoto(panel) {
      const img = panel.querySelector('[data-wi-profile-avatar]');
      const src = img ? img.getAttribute('src') : '';
      if (!src) {
        this.profilePhotoMessage(panel, 'No profile picture to preview.', 'error');
        return;
      }
      this.profilePhotoModal('Profile picture preview', '<div class="wi-profile-photo-large"><img src="' + this.escape(src) + '" alt="Profile picture preview"></div>');
    },

    openProfilePhotoCamera(panel) {
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        this.profilePhotoMessage(panel, 'Camera is not available in this browser.', 'error');
        return;
      }
      const modal = this.profilePhotoModal('Take profile picture', '<div class="wi-profile-camera"><video autoplay playsinline></video><canvas hidden></canvas><div class="wi-profile-camera-actions"><button type="button" data-wi-camera-capture>Capture & use</button></div></div>');
      const video = modal.querySelector('video');
      const canvas = modal.querySelector('canvas');
      let stream = null;
      navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then((mediaStream) => {
          stream = mediaStream;
          video.srcObject = mediaStream;
        })
        .catch(() => {
          this.closeProfilePhotoModal(modal);
          this.profilePhotoMessage(panel, 'Could not open the camera.', 'error');
        });
      modal.querySelector('[data-wi-camera-capture]').addEventListener('click', () => {
        if (!video || !canvas) return;
        canvas.width = video.videoWidth || 720;
        canvas.height = video.videoHeight || 720;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob((blob) => {
          if (stream) stream.getTracks().forEach((track) => track.stop());
          this.closeProfilePhotoModal(modal);
          if (blob) this.uploadProfilePhoto(panel, new File([blob], 'profile-camera.jpg', { type: 'image/jpeg' }));
        }, 'image/jpeg', 0.88);
      });
      modal.addEventListener('wi-profile-photo-modal-close', () => {
        if (stream) stream.getTracks().forEach((track) => track.stop());
      });
    },

    profilePhotoModal(title, bodyHtml) {
      this.closeProfilePhotoModal(document.querySelector('[data-wi-profile-photo-modal]'));
      const modal = document.createElement('div');
      modal.setAttribute('data-wi-profile-photo-modal', '1');
      modal.className = 'wi-profile-photo-modal';
      modal.innerHTML = '<div class="wi-profile-photo-modal__dialog">' +
        '<header><h2>' + this.escape(title) + '</h2><button type="button" data-wi-profile-photo-modal-close aria-label="Close">×</button></header>' +
        '<div class="wi-profile-photo-modal__body" data-wi-profile-photo-modal-body>' + bodyHtml + '</div>' +
        '</div>';
      document.body.appendChild(modal);
      modal.querySelector('[data-wi-profile-photo-modal-close]').addEventListener('click', () => this.closeProfilePhotoModal(modal));
      modal.addEventListener('click', (event) => { if (event.target === modal) this.closeProfilePhotoModal(modal); });
      return modal;
    },

    closeProfilePhotoModal(modal) {
      if (!modal) return;
      modal.dispatchEvent(new CustomEvent('wi-profile-photo-modal-close'));
      modal.remove();
    },

    escape(value) {
      return String(value || '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
    },

    bindThemeToggle() {      const button = document.querySelector('[data-wi-theme-toggle]');
      if (!button) return;
      button.addEventListener('click', () => {
        document.body.classList.toggle('wi-members-light');
        window.localStorage.setItem('wi_members_theme', document.body.classList.contains('wi-members-light') ? 'light' : 'dark');
      });
      if (window.localStorage.getItem('wi_members_theme') === 'light') document.body.classList.add('wi-members-light');
    },

    decorateIcons() {
      document.querySelectorAll('[data-icon]').forEach((node) => node.setAttribute('aria-hidden', 'true'));
    }
  };

  window.WIMembers = WIMembers;
  window.WIProfile = WIMembers;
  window.WISettings = WIMembers;
  window.WIPayments = WIMembers;
  window.WIForms = WIMembers;

  document.addEventListener('DOMContentLoaded', () => WIMembers.init());
})(window, document);
