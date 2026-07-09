/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WICompliance
| File: WIModal.js
| Location: /root/WIAdmin/WICore/WIJ/WIModal.js
| Type: JavaScript
| Layer: Shared Admin UI
| Purpose Area: Shared modal frontend bridge
| Version: 1.0.0
| Created: 2026-05-17
| Last Updated: 2026-05-17
| Status: Production Ready
|--------------------------------------------------------------------------
| Summary:
| Shared lightweight frontend modal helper for admin-side UI actions.
|
| This is intentionally generic. It does not contain Equipment, Sites,
| Compliance, Media, or product-specific logic.
|--------------------------------------------------------------------------
*/

(function (window, document) {
    'use strict';

    if (window.WIModal && window.WIModal.__wiSharedModal) {
        return;
    }

    var activeModal = null;

    function escapeHtml(value) {
        return String(value === null || typeof value === 'undefined' ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function ensureStyles() {
        if (document.getElementById('wiSharedModalStyles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'wiSharedModalStyles';
        style.textContent = [
            '.wi-modal-overlay{position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;background:rgba(2,6,23,.72);backdrop-filter:blur(8px);padding:18px;}',
            '.wi-modal{width:min(760px,96vw);max-height:92vh;overflow:hidden;border:1px solid rgba(255,255,255,.18);border-radius:22px;background:linear-gradient(145deg,#07111f,#111827);box-shadow:0 28px 80px rgba(0,0,0,.45);color:#f8fafc;display:flex;flex-direction:column;}',
            '.wi-modal--sm{width:min(460px,96vw);}',
            '.wi-modal--lg{width:min(960px,96vw);}',
            '.wi-modal--xl{width:min(1180px,97vw);}',
            '.wi-modal-header{display:flex;align-items:center;justify-content:space-between;gap:14px;border-bottom:1px solid rgba(255,255,255,.10);padding:16px 18px;}',
            '.wi-modal-title{margin:0;font-size:18px;font-weight:900;color:#fff;}',
            '.wi-modal-close{border:1px solid rgba(255,255,255,.16);border-radius:999px;background:rgba(255,255,255,.06);color:#fff;width:34px;height:34px;display:grid;place-items:center;cursor:pointer;font-size:20px;line-height:1;}',
            '.wi-modal-content{padding:18px;overflow:auto;}',
            '.wi-modal-content .wi-field{display:grid;gap:7px;margin-bottom:13px;}',
            '.wi-modal-content .wi-field label{font-size:12px;font-weight:850;color:#cbd5e1;}',
            '.wi-modal-content .wi-field input,.wi-modal-content .wi-field select,.wi-modal-content .wi-field textarea{width:100%;min-height:40px;border:1px solid rgba(148,163,184,.22);border-radius:13px;background:rgba(2,6,23,.42);color:#fff;padding:9px 11px;outline:none;}',
            '.wi-modal-content .wi-field textarea{min-height:96px;resize:vertical;}',
            '.wi-equipment-modal-actions,.wi-modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:16px;}',
            '.wi-btn{border:1px solid rgba(255,255,255,.18);border-radius:999px;background:rgba(255,255,255,.06);color:#fff;padding:9px 14px;font-weight:850;cursor:pointer;}',
            '.wi-btn-primary{background:rgba(34,211,238,.16);border-color:rgba(34,211,238,.42);color:#cffafe;}',
            '.wi-btn-ghost{background:rgba(255,255,255,.05);}',
            '.wi-btn-danger{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.45);color:#fecaca;}'
        ].join('');

        document.head.appendChild(style);
    }

    function close() {
        if (!activeModal) {
            return;
        }

        activeModal.remove();
        activeModal = null;
        document.documentElement.classList.remove('wi-modal-open');
    }

    function open(options) {
        options = options || {};

        close();
        ensureStyles();

        var title = options.title || 'Modal';
        var content = options.content || '';
        var size = options.size || 'lg';
        var className = options.className || '';

        var overlay = document.createElement('div');
        overlay.className = 'wi-modal-overlay';
        overlay.setAttribute('data-wi-modal-overlay', '1');

        overlay.innerHTML = [
            '<section class="wi-modal wi-modal--' + escapeHtml(size) + ' ' + escapeHtml(className) + '" role="dialog" aria-modal="true">',
                '<header class="wi-modal-header">',
                    '<h2 class="wi-modal-title">' + escapeHtml(title) + '</h2>',
                    '<button type="button" class="wi-modal-close" data-wi-modal-close aria-label="Close modal">&times;</button>',
                '</header>',
                '<div class="wi-modal-content" data-wi-modal-content>',
                    content,
                '</div>',
            '</section>'
        ].join('');

        document.body.appendChild(overlay);
        document.documentElement.classList.add('wi-modal-open');

        activeModal = overlay;

        return overlay;
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-wi-modal-close]')) {
            event.preventDefault();
            close();
            return;
        }

        if (event.target.matches('[data-wi-modal-overlay]')) {
            close();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            close();
        }
    });

    window.WIModal = {
        __wiSharedModal: true,
        open: open,
        show: function (title, content, options) {
            options = options || {};
            options.title = title;
            options.content = content;
            return open(options);
        },
        close: close
    };
})(window, document);