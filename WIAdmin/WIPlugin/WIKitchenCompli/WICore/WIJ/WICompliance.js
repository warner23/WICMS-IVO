(function (window, document) {
  'use strict';

  const ajaxUrl = 'WICore/WIClass/WIAjax.php';

  async function post(action, payload) {
    const formData = new URLSearchParams();
    formData.append('action', action);

    Object.keys(payload || {}).forEach((key) => {
      formData.append(key, payload[key]);
    });

    const response = await fetch(ajaxUrl, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: formData.toString()
    });

    const contentType = response.headers.get('content-type') || '';
    if (contentType.indexOf('application/json') !== -1) {
      return response.json();
    }
    return response.text();
  }

  async function hydrateDashboard() {
    const target = document.getElementById('wic-dashboard');
    if (!target) return;
    const html = await post('haveposts', {});
    target.innerHTML = html;
  }

  async function bindForms() {
    document.querySelectorAll('[data-wic-form]').forEach((form) => {
      form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const action = form.getAttribute('data-wic-form');
        const payload = {};
        new FormData(form).forEach((value, key) => {
          payload[key] = value;
        });

        const result = await post(action, payload);
        const output = form.querySelector('.wic-form-output');
        if (output) {
          output.textContent = result.message || 'Saved.';
        }
        if (action === 'addChecklist' || action === 'addLegalItem' || action === 'addReminder') {
          hydrateDashboard();
        }
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    hydrateDashboard();
    bindForms();
  });

  window.WIComplianceUI = {
    refresh: hydrateDashboard,
    post
  };
})(window, document);
