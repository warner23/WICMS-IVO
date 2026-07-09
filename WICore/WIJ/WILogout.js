/**
 * FILE:
 * WICMS-IVO/WICore/WIJ/WILogout.js
 *
 * Canonical logout helper for WICMS.
 */

var WILogout = (function () {

    function init() {
        $(document).on('click', '[data-wi-logout]', function (e) {
            const target = $(this);
            const href = target.attr('href') || 'logout.php';

            e.preventDefault();
            window.location.href = href;
        });
    }

    $(document).ready(function () {
        init();
    });

    return {
        init: init
    };

})();