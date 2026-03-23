(function (window, document) {
    'use strict';

    const WIDashboard = {
        selectors: {
            page: '.wi-dashboard-page',
            statCards: '.wi-stat-card',
            quickActionCards: '.wi-quick-action-card',
            featureCards: '.wi-feature-card',
            availableCards: '.wi-available-card',
            healthRows: '.wi-health-row',
            panelHeaders: '.wi-dashboard-panel__header'
        },

        init() {
            if (!document.querySelector(this.selectors.page)) {
                return;
            }

            this.bindQuickActions();
            this.decorateCards();
            this.decorateHealthRows();
            this.decoratePanels();
            this.runIntroState();
        },

        bindQuickActions() {
            const quickActions = document.querySelectorAll(this.selectors.quickActionCards);

            if (!quickActions.length) {
                return;
            }

            quickActions.forEach((card) => {
                card.addEventListener('keydown', (event) => {
                    const key = event.key || event.code;

                    if (key === 'Enter' || key === ' ') {
                        event.preventDefault();
                        card.click();
                    }
                });
            });
        },

        decorateCards() {
            const groups = [
                this.selectors.statCards,
                this.selectors.featureCards,
                this.selectors.availableCards
            ];

            groups.forEach((selector) => {
                const items = document.querySelectorAll(selector);

                if (!items.length) {
                    return;
                }

                items.forEach((item, index) => {
                    item.setAttribute('data-wi-ready', 'true');
                    item.style.setProperty('--wi-item-index', String(index));
                });
            });
        },

        decorateHealthRows() {
            const rows = document.querySelectorAll(this.selectors.healthRows);

            if (!rows.length) {
                return;
            }

            rows.forEach((row) => {
                const strong = row.querySelector('strong');
                if (!strong) {
                    return;
                }

                const text = strong.textContent ? strong.textContent.trim().toLowerCase() : '';

                if (text.includes('ok')) {
                    row.setAttribute('data-health-state', 'ok');
                } else if (text.includes('check')) {
                    row.setAttribute('data-health-state', 'check');
                } else {
                    row.setAttribute('data-health-state', 'info');
                }
            });
        },

        decoratePanels() {
            const headers = document.querySelectorAll(this.selectors.panelHeaders);

            if (!headers.length) {
                return;
            }

            headers.forEach((header) => {
                const panel = header.closest('.wi-dashboard-panel');
                if (!panel) {
                    return;
                }

                panel.setAttribute('data-panel-ready', 'true');
            });
        },

        runIntroState() {
            const page = document.querySelector(this.selectors.page);
            if (!page) {
                return;
            }

            window.requestAnimationFrame(() => {
                page.classList.add('wi-dashboard-ready');
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            WIDashboard.init();
        });
    } else {
        WIDashboard.init();
    }

    window.WIDashboard = WIDashboard;
})(window, document);