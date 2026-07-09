/*
|--------------------------------------------------------------------------
| WIAccessibility - Site-wide Accessibility Tools
| Product: WICMS / WI Shared Core
| Version: 1.0.0
|--------------------------------------------------------------------------
*/

(function () {
    'use strict';

    const defaults = {
        theme: 'system',
        contrast: false,
        text: 'normal',
        readable: false,
        reducedMotion: false
    };

    function storageKey(root) {
        return (root && root.dataset && root.dataset.storageKey) || 'wi.accessibility.v1';
    }

    function readSettings(root) {
        try {
            const raw = window.localStorage.getItem(storageKey(root));
            if (!raw) {
                return Object.assign({}, defaults);
            }

            return Object.assign({}, defaults, JSON.parse(raw));
        } catch (error) {
            return Object.assign({}, defaults);
        }
    }

    function saveSettings(root, settings) {
        try {
            window.localStorage.setItem(storageKey(root), JSON.stringify(settings));
        } catch (error) {
            // localStorage can be unavailable in private/restricted browsers.
        }
    }

    function prefersDark() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function effectiveTheme(theme) {
        if (theme === 'dark' || theme === 'light') {
            return theme;
        }

        return prefersDark() ? 'dark' : 'light';
    }

    function applySettings(settings) {
        const html = document.documentElement;
        const theme = effectiveTheme(settings.theme);

        html.setAttribute('data-wi-a11y-active', 'true');
        html.setAttribute('data-wi-theme', theme);
        html.setAttribute('data-wi-contrast', settings.contrast ? 'high' : 'normal');
        html.setAttribute('data-wi-text', settings.text || 'normal');
        html.setAttribute('data-wi-readable', settings.readable ? 'on' : 'off');
        html.setAttribute('data-wi-reduced-motion', settings.reducedMotion ? 'on' : 'off');
    }

    function setFeedback(root, message) {
        const feedback = root.querySelector('.wi-accessibility-feedback');
        if (feedback) {
            feedback.textContent = message || '';
        }
    }

    function setActiveButtons(root, settings) {
        root.querySelectorAll('[data-wi-a11y-theme]').forEach(function (button) {
            button.classList.toggle('is-active', button.dataset.wiA11yTheme === settings.theme);
        });

        root.querySelectorAll('[data-wi-a11y-text]').forEach(function (button) {
            button.classList.toggle('is-active', button.dataset.wiA11yText === settings.text);
        });

        const map = {
            contrast: !!settings.contrast,
            readable: !!settings.readable,
            motion: !!settings.reducedMotion
        };

        root.querySelectorAll('[data-wi-a11y-toggle]').forEach(function (button) {
            button.classList.toggle('is-active', !!map[button.dataset.wiA11yToggle]);
        });
    }

    function openPanel(root) {
        root.classList.add('is-open');
        const panel = root.querySelector('.wi-accessibility-panel');
        if (panel) {
            panel.setAttribute('aria-hidden', 'false');
        }
    }

    function closePanel(root) {
        root.classList.remove('is-open');
        const panel = root.querySelector('.wi-accessibility-panel');
        if (panel) {
            panel.setAttribute('aria-hidden', 'true');
        }
    }

    function getReadableText() {
        const selected = String(window.getSelection ? window.getSelection() : '').trim();
        if (selected) {
            return selected;
        }

        const clone = document.body.cloneNode(true);
        clone.querySelectorAll('script, style, noscript, iframe, input, select, textarea, button, .wi-accessibility-root, .wi-bug-reporter-root').forEach(function (node) {
            node.remove();
        });

        return (clone.innerText || clone.textContent || '')
            .replace(/\s+/g, ' ')
            .trim()
            .substring(0, 9000);
    }

    function speak(text, root) {
        if (!('speechSynthesis' in window) || !('SpeechSynthesisUtterance' in window)) {
            setFeedback(root, 'Read-aloud is not supported by this browser.');
            return;
        }

        const content = String(text || '').trim();
        if (!content) {
            setFeedback(root, 'No readable text found.');
            return;
        }

        window.speechSynthesis.cancel();

        const utterance = new SpeechSynthesisUtterance(content);
        utterance.rate = 0.95;
        utterance.pitch = 1;
        utterance.volume = 1;

        utterance.onstart = function () {
            setFeedback(root, 'Reading aloud...');
        };

        utterance.onend = function () {
            setFeedback(root, 'Read-aloud finished.');
        };

        utterance.onerror = function () {
            setFeedback(root, 'Read-aloud stopped or failed.');
        };

        window.speechSynthesis.speak(utterance);
    }

    function stopSpeaking(root) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
        setFeedback(root, 'Read-aloud stopped.');
    }

    function bindRoot(root) {
        if (root.dataset.wiAccessibilityBound === '1') {
            return;
        }
        root.dataset.wiAccessibilityBound = '1';

        let settings = readSettings(root);
        applySettings(settings);
        setActiveButtons(root, settings);

        const toggle = root.querySelector('.wi-accessibility-toggle');
        const close = root.querySelector('.wi-accessibility-close');

        if (toggle) {
            toggle.addEventListener('click', function () {
                openPanel(root);
            });
        }

        if (close) {
            close.addEventListener('click', function () {
                closePanel(root);
            });
        }

        root.querySelectorAll('[data-wi-a11y-theme]').forEach(function (button) {
            button.addEventListener('click', function () {
                settings.theme = button.dataset.wiA11yTheme || 'system';
                applySettings(settings);
                saveSettings(root, settings);
                setActiveButtons(root, settings);
                setFeedback(root, 'Theme preference saved.');
            });
        });

        root.querySelectorAll('[data-wi-a11y-text]').forEach(function (button) {
            button.addEventListener('click', function () {
                settings.text = button.dataset.wiA11yText || 'normal';
                applySettings(settings);
                saveSettings(root, settings);
                setActiveButtons(root, settings);
                setFeedback(root, 'Text size preference saved.');
            });
        });

        root.querySelectorAll('[data-wi-a11y-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const mode = button.dataset.wiA11yToggle;

                if (mode === 'contrast') {
                    settings.contrast = !settings.contrast;
                    setFeedback(root, settings.contrast ? 'High contrast enabled.' : 'High contrast disabled.');
                }

                if (mode === 'readable') {
                    settings.readable = !settings.readable;
                    setFeedback(root, settings.readable ? 'Readable spacing enabled.' : 'Readable spacing disabled.');
                }

                if (mode === 'motion') {
                    settings.reducedMotion = !settings.reducedMotion;
                    setFeedback(root, settings.reducedMotion ? 'Reduced motion enabled.' : 'Reduced motion disabled.');
                }

                applySettings(settings);
                saveSettings(root, settings);
                setActiveButtons(root, settings);
            });
        });

        root.querySelectorAll('[data-wi-a11y-speak]').forEach(function (button) {
            button.addEventListener('click', function () {
                const mode = button.dataset.wiA11ySpeak;

                if (mode === 'stop') {
                    stopSpeaking(root);
                    return;
                }

                if (mode === 'selection') {
                    const selected = String(window.getSelection ? window.getSelection() : '').trim();
                    speak(selected, root);
                    return;
                }

                speak(getReadableText(), root);
            });
        });

        const reset = root.querySelector('[data-wi-a11y-reset]');
        if (reset) {
            reset.addEventListener('click', function () {
                settings = Object.assign({}, defaults);
                stopSpeaking(root);
                applySettings(settings);
                saveSettings(root, settings);
                setActiveButtons(root, settings);
                setFeedback(root, 'Accessibility settings reset.');
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && root.classList.contains('is-open')) {
                closePanel(root);
            }
        });
    }

    function boot() {
        document.querySelectorAll('.wi-accessibility-root').forEach(bindRoot);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    if (window.matchMedia) {
        const media = window.matchMedia('(prefers-color-scheme: dark)');
        const onChange = function () {
            const root = document.querySelector('.wi-accessibility-root');
            if (!root) {
                return;
            }
            const settings = readSettings(root);
            if (settings.theme === 'system') {
                applySettings(settings);
            }
        };

        if (media.addEventListener) {
            media.addEventListener('change', onChange);
        } else if (media.addListener) {
            media.addListener(onChange);
        }
    }
})();
