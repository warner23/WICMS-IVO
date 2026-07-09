/**
 * File Information
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WIProfile / WITaskEngine
 * Project: WI Ecosystem
 * File: WIProfileTasks.js
 * Location: /WIMembers/WICore/WIJ/WIProfileTasks.js
 * Type: JavaScript
 * Layer: Member workspace UI behaviour
 * Purpose Area: Loads and manages WITaskEngine task widgets inside WIProfile.
 * Version: 1.4.0-bridge-batch-4
 * Created: 2026-06-03
 * Last Updated: 2026-06-03
 * Status: Production-ready bridge batch 4
 */
(function () {
  'use strict';

  const SELECTOR = '[data-wi-profile-tasks]';

  function init(root) {
    const scope = root || document;
    scope.querySelectorAll(SELECTOR).forEach((panel) => {
      if (panel.dataset.wiProfileTasksReady === '1') return;
      panel.dataset.wiProfileTasksReady = '1';
      bind(panel);
      load(panel);
    });
  }

  function bind(panel) {
    panel.addEventListener('click', function (event) {
      const target = event.target.closest('[data-profile-task-action]');
      if (!target) return;
      event.preventDefault();

      const action = target.dataset.profileTaskAction;
      const taskId = parseInt(target.dataset.taskId || '0', 10);
      if (action === 'refresh') {
        load(panel);
        return;
      }
      if (action === 'complete' && taskId > 0) {
        complete(panel, taskId);
      }
    });

    panel.querySelectorAll('[data-profile-task-filter]').forEach((input) => {
      input.addEventListener('change', () => load(panel));
    });
  }

  function endpoint(panel) {
    return panel.dataset.ajaxEndpoint || 'WICore/WIAjax/WIMembersAjax.php';
  }

  function csrf(panel) {
    return panel.dataset.csrfToken || '';
  }

  function filters(panel) {
    const status = panel.querySelector('[name="profile_task_status"]');
    const priority = panel.querySelector('[name="profile_task_priority"]');
    return {
      status: status ? status.value : 'all',
      priority: priority ? priority.value : 'all'
    };
  }

  async function post(panel, data) {
    const form = new FormData();
    Object.entries(data).forEach(([key, value]) => form.append(key, value));
    form.append('csrf_token', csrf(panel));

    const response = await fetch(endpoint(panel), {
      method: 'POST',
      credentials: 'same-origin',
      body: form
    });

    const text = await response.text();
    try {
      return JSON.parse(text);
    } catch (err) {
      return { success: false, message: 'AJAX did not return JSON.', raw: text };
    }
  }

  async function load(panel) {
    setState(panel, 'Loading tasks…');
    const payload = await post(panel, Object.assign({ action: 'member_profile_tasks' }, filters(panel)));
    if (!payload.success) {
      setError(panel, payload.message || 'Could not load tasks.', payload.raw || '');
      return;
    }
    render(panel, payload);
  }

  async function complete(panel, taskId) {
    const payload = await post(panel, {
      action: 'member_profile_task_complete',
      task_id: taskId
    });
    if (!payload.success) {
      setError(panel, payload.message || 'Task could not be completed.', payload.raw || '');
      return;
    }
    load(panel);
  }

  function render(panel, payload) {
    const summary = payload.summary || {};
    const tasks = Array.isArray(payload.tasks) ? payload.tasks : [];
    const summaryNode = panel.querySelector('[data-profile-task-summary]');
    const listNode = panel.querySelector('[data-profile-task-list]');

    if (summaryNode) {
      summaryNode.innerHTML = [
        metric('Open', summary.open || 0, 'open'),
        metric('Due today', summary.due_today || 0, 'today'),
        metric('Overdue', summary.overdue || 0, 'overdue'),
        metric('Proof needed', summary.awaiting_proof || 0, 'proof')
      ].join('');
    }

    if (!listNode) return;
    if (tasks.length === 0) {
      listNode.innerHTML = '<div class="wi-profile-task-empty">No tasks assigned to you for this filter.</div>';
      return;
    }

    listNode.innerHTML = tasks.map(taskCard).join('');
  }

  function metric(label, value, key) {
    return '<div class="wi-profile-task-metric wi-profile-task-metric--' + escapeHtml(key) + '">' +
      '<strong>' + escapeHtml(String(value)) + '</strong><span>' + escapeHtml(label) + '</span></div>';
  }

  function taskCard(task) {
    const id = parseInt(task.id || 0, 10);
    const state = String(task.state || 'grey');
    const status = String(task.status_code || 'open');
    const due = task.due_at ? escapeHtml(String(task.due_at)) : 'No due date';
    const canComplete = !['completed', 'cancelled', 'archived'].includes(status);

    return '<article class="wi-profile-task-card wi-profile-task-card--' + escapeHtml(state) + '">' +
      '<div><div class="wi-profile-task-title">' + escapeHtml(String(task.title || 'Task')) + '</div>' +
      '<div class="wi-profile-task-meta">' + escapeHtml(String(task.queue_code || 'operations')) + ' · ' + escapeHtml(String(task.priority_code || 'normal')) + ' · ' + due + '</div>' +
      (task.description ? '<p>' + escapeHtml(String(task.description)).slice(0, 220) + '</p>' : '') + '</div>' +
      '<div class="wi-profile-task-actions">' +
      '<span class="wi-profile-task-badge">' + escapeHtml(status) + '</span>' +
      (canComplete ? '<button type="button" data-profile-task-action="complete" data-task-id="' + id + '">Complete</button>' : '') +
      '</div></article>';
  }

  function setState(panel, message) {
    const listNode = panel.querySelector('[data-profile-task-list]');
    if (listNode) listNode.innerHTML = '<div class="wi-profile-task-empty">' + escapeHtml(message) + '</div>';
  }

  function setError(panel, message, raw) {
    const listNode = panel.querySelector('[data-profile-task-list]');
    if (listNode) {
      listNode.innerHTML = '<div class="wi-profile-task-error"><strong>' + escapeHtml(message) + '</strong>' +
        (raw ? '<pre>' + escapeHtml(String(raw)).slice(0, 800) + '</pre>' : '') + '</div>';
    }
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>'"]/g, function (char) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[char];
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => init(document));
  } else {
    init(document);
  }

  window.WIProfileTasks = { init: init };
})();
