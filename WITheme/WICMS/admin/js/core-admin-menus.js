/*
|--------------------------------------------------------------------------
| WICMS Core Admin Menu UI
|--------------------------------------------------------------------------
*/
(function () {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    function qsa(root, selector) {
        return Array.prototype.slice.call((root || document).querySelectorAll(selector));
    }

    function findTabsRoot() {
        return document.querySelector('[data-wi-menu-tabs]');
    }

    function getLinks(root) {
        return qsa(root, '[data-wi-menu-tab]');
    }

    function getPanels(root) {
        return qsa(root, '[data-wi-menu-panel]');
    }

    function activateTab(root, targetId, persist) {
        var links = getLinks(root);
        var panels = getPanels(root);
        var found = false;

        panels.forEach(function (panel) {
            var active = panel.getAttribute('data-wi-menu-panel') === targetId || panel.id === targetId;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
            if (active) {
                found = true;
            }
        });

        if (!found && panels.length) {
            targetId = panels[0].getAttribute('data-wi-menu-panel') || panels[0].id;
            panels[0].classList.add('is-active');
            panels[0].hidden = false;
        }

        links.forEach(function (link) {
            var active = link.getAttribute('data-wi-menu-tab') === targetId;
            link.classList.toggle('is-active', active);
            link.setAttribute('aria-selected', active ? 'true' : 'false');
            link.setAttribute('tabindex', active ? '0' : '-1');
        });

        if (persist !== false) {
            try {
                window.sessionStorage.setItem('wi_menu_active_tab_id', targetId);
            } catch (e) {}
        }
    }

    function bootTabs() {
        var root = findTabsRoot();
        if (!root) {
            return;
        }

        var links = getLinks(root);
        var panels = getPanels(root);
        if (!links.length || !panels.length) {
            return;
        }

        root.classList.add('wi-menu-tabs-ready');

        links.forEach(function (link) {
            var targetId = link.getAttribute('data-wi-menu-tab');
            link.addEventListener('click', function (event) {
                event.preventDefault();
                activateTab(root, targetId, true);
            });
        });

        var startId = '';
        try {
            startId = window.sessionStorage.getItem('wi_menu_active_tab_id') || '';
        } catch (e) {
            startId = '';
        }

        if (!startId || !document.querySelector('[data-wi-menu-panel="' + startId + '"]')) {
            startId = links[0].getAttribute('data-wi-menu-tab');
        }

        activateTab(root, startId, false);
    }

    function closestSortableContainer(item) {
        return item.parentElement;
    }

    function makeSortableItem(item) {
        if (item.getAttribute('data-wi-sortable-ready') === '1') {
            return;
        }

        item.setAttribute('data-wi-sortable-ready', '1');
        item.setAttribute('draggable', 'true');

        item.addEventListener('dragstart', function () {
            item.classList.add('is-dragging');
        });

        item.addEventListener('dragend', function () {
            item.classList.remove('is-dragging');
            qsa(document, '.wi-drag-over').forEach(function (el) {
                el.classList.remove('wi-drag-over');
            });
        });

        item.addEventListener('dragover', function (event) {
            var dragging = document.querySelector('.is-dragging');
            var container = closestSortableContainer(item);

            if (!dragging || !container || dragging === item || closestSortableContainer(dragging) !== container) {
                return;
            }

            event.preventDefault();
            item.classList.add('wi-drag-over');

            var rect = item.getBoundingClientRect();
            var after = (event.clientY - rect.top) > (rect.height / 2);

            if (after) {
                container.insertBefore(dragging, item.nextSibling);
            } else {
                container.insertBefore(dragging, item);
            }
        });

        item.addEventListener('dragleave', function () {
            item.classList.remove('wi-drag-over');
        });
    }

    function bootSortables() {
        qsa(document, '#wi-site-menu-sortable [data-menu-id], .wi-admin-menu-item[data-menu-id], #editaccordion [data-sidebar-id]').forEach(makeSortableItem);
    }

    function bootSidebarAccordion() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.accordion) {
            return;
        }

        var accordion = jQuery('#editaccordion');
        if (accordion.length) {
            accordion.accordion({
                collapsible: true,
                heightStyle: 'content',
                active: false
            });
        }
    }

    ready(function () {
        bootTabs();
        bootSortables();
        bootSidebarAccordion();
    });
})();
