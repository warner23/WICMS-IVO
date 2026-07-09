(() => {
  'use strict';
  const root = document.querySelector('[data-wi-user-notifications-root]');
  if (!root) return;
  const list = root.querySelector('[data-wi-user-notifications-list]');
  const esc = (value) => String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
  fetch('/WICore/WIAjax/WINotificationsAjax.php?action=winotifications_workspace', { credentials: 'same-origin' })
    .then((response) => response.json())
    .then((result) => {
      const records = ((result || {}).data || {}).records || [];
      list.innerHTML = records.length ? records.map((row) => `<article class="wi-user-notification"><h3>${esc(row.title)}</h3><p>${esc(row.body || row.notes || '')}</p></article>`).join('') : '<div class="wi-user-empty">No notifications yet.</div>';
    })
    .catch(() => { list.innerHTML = '<div class="wi-user-empty">Notifications could not be loaded.</div>'; });
})();
