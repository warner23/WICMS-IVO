(function (document) {
    'use strict';
    document.querySelectorAll('[data-wi-user-plugin="wirotas"]').forEach(function (root) {
        root.classList.add('is-ready');
    });
})(document);
