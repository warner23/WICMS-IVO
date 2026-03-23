(function () {
    'use strict';

    const config = window.WIMediaConfig || {};
    const ajaxUrl = config.ajaxUrl || '';
    const csrfToken = config.csrfToken || '';

    const page = document.getElementById('wi-media-page');
    if (!page || !ajaxUrl) {
        return;
    }

    const uploadPanel = document.getElementById('wi-media-upload-panel');
    const openUploadBtn = document.getElementById('wi-open-upload-panel');
    const closeUploadBtn = document.getElementById('wi-close-upload-panel');

    const uploadForm = document.getElementById('wi-media-upload-form');
    const uploadMessage = document.getElementById('wi-media-upload-message');
    const uploadSubmitBtn = document.getElementById('wi-media-upload-submit');

    const mediaGrid = document.getElementById('wi-media-grid');
    const mediaCount = document.getElementById('wi-media-count');
    const listMessage = document.getElementById('wi-media-list-message');

    const applyFiltersBtn = document.getElementById('wi-media-apply-filters');
    const resetFiltersBtn = document.getElementById('wi-media-reset-filters');

    const searchInput = document.getElementById('wi-media-search');
    const typeFilter = document.getElementById('wi-media-type-filter');
    const statusFilter = document.getElementById('wi-media-status-filter');
    const folderFilter = document.getElementById('wi-media-folder-filter');

    const editModal = document.getElementById('wi-media-edit-modal');
    const editCloseBtn = document.getElementById('wi-media-edit-close');
    const editCancelBtn = document.getElementById('wi-media-edit-cancel');
    const editForm = document.getElementById('wi-media-edit-form');
    const editMessage = document.getElementById('wi-media-edit-message');
    const editPreview = document.getElementById('wi-media-edit-preview');

    const editId = document.getElementById('wi-edit-id');
    const editTitle = document.getElementById('wi-edit-title');
    const editAltText = document.getElementById('wi-edit-alt-text');
    const editFolder = document.getElementById('wi-edit-folder');
    const editStatus = document.getElementById('wi-edit-status');
    const editPrivate = document.getElementById('wi-edit-private');
    const editCaption = document.getElementById('wi-edit-caption');
    const editDescription = document.getElementById('wi-edit-description');

    let currentFilters = {
        search: '',
        media_type: '',
        status: '',
        folder: '',
        limit: 50,
        offset: 0
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function nl2br(value) {
        return escapeHtml(value).replace(/\n/g, '<br>');
    }

    function setMessage(element, type, message) {
        if (!element) {
            return;
        }

        if (!message) {
            element.innerHTML = '';
            element.className = 'wi-message-area';
            return;
        }

        element.className = 'wi-message-area wi-message-' + type;
        element.innerHTML = '<div class="wi-message wi-message-' + type + '">' + escapeHtml(message) + '</div>';
    }

    function formatBytes(bytes) {
        const value = Number(bytes || 0);

        if (value < 1024) {
            return value + ' B';
        }
        if (value < 1024 * 1024) {
            return (value / 1024).toFixed(2) + ' KB';
        }
        if (value < 1024 * 1024 * 1024) {
            return (value / (1024 * 1024)).toFixed(2) + ' MB';
        }

        return (value / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
    }

    function formatDate(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString();
    }

    function getFileIcon(mediaType) {
        switch (mediaType) {
            case 'image':
                return '🖼️';
            case 'video':
                return '🎬';
            case 'audio':
                return '🎵';
            case 'document':
                return '📄';
            default:
                return '📁';
        }
    }

    function isImage(item) {
        return item && item.media_type === 'image' && item.file_url;
    }

    function renderPreview(item) {
        if (isImage(item)) {
            return `
                <div class="wi-media-card-thumb">
                    <img src="${escapeHtml(item.file_url)}" alt="${escapeHtml(item.alt_text || item.title || item.original_name || '')}">
                </div>
            `;
        }

        return `
            <div class="wi-media-card-thumb wi-media-card-thumb-icon">
                <span class="wi-media-file-icon">${getFileIcon(item.media_type)}</span>
            </div>
        `;
    }

    function renderBadge(text, extraClass) {
        return `<span class="wi-badge ${extraClass || ''}">${escapeHtml(text)}</span>`;
    }

    function renderMediaCard(item) {
        const title = item.title || item.original_name || 'Untitled';
        const folder = item.folder || '—';
        const privateLabel = Number(item.is_private) === 1 ? renderBadge('Private', 'wi-badge-dark') : '';
        const statusLabel = Number(item.status) === 1
            ? renderBadge('Active', 'wi-badge-success')
            : renderBadge('Inactive', 'wi-badge-muted');

        return `
            <div class="wi-media-card" data-id="${Number(item.id)}">
                ${renderPreview(item)}

                <div class="wi-media-card-body">
                    <h4 class="wi-media-card-title" title="${escapeHtml(title)}">${escapeHtml(title)}</h4>

                    <div class="wi-media-card-meta">
                        <div><strong>File:</strong> ${escapeHtml(item.original_name || '—')}</div>
                        <div><strong>Type:</strong> ${escapeHtml(item.media_type || '—')}</div>
                        <div><strong>Folder:</strong> ${escapeHtml(folder)}</div>
                        <div><strong>Size:</strong> ${formatBytes(item.file_size)}</div>
                        <div><strong>Uploaded:</strong> ${escapeHtml(formatDate(item.created_at))}</div>
                    </div>

                    <div class="wi-media-card-badges">
                        ${statusLabel}
                        ${privateLabel}
                    </div>

                    <div class="wi-media-card-actions">
                        <button type="button" class="wi-btn wi-btn-secondary wi-media-edit-btn" data-id="${Number(item.id)}">
                            Edit
                        </button>
                        <button type="button" class="wi-btn wi-btn-danger wi-media-delete-btn" data-id="${Number(item.id)}">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function renderEmptyState(message) {
        mediaGrid.innerHTML = `<div class="wi-media-empty">${escapeHtml(message)}</div>`;
    }

    async function postFormData(formData) {
        const response = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    async function postUrlEncoded(data) {
        const body = new URLSearchParams();

        Object.keys(data).forEach(function (key) {
            if (data[key] !== null && data[key] !== undefined) {
                body.append(key, String(data[key]));
            }
        });

        const response = await fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString(),
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Request failed with status ' + response.status);
        }

        return response.json();
    }

    function collectFilters() {
        currentFilters.search = (searchInput?.value || '').trim();
        currentFilters.media_type = typeFilter?.value || '';
        currentFilters.status = statusFilter?.value || '';
        currentFilters.folder = (folderFilter?.value || '').trim();
        currentFilters.offset = 0;
    }

    function resetFilters() {
        if (searchInput) searchInput.value = '';
        if (typeFilter) typeFilter.value = '';
        if (statusFilter) statusFilter.value = '';
        if (folderFilter) folderFilter.value = '';

        currentFilters = {
            search: '',
            media_type: '',
            status: '',
            folder: '',
            limit: 50,
            offset: 0
        };
    }

    async function loadMediaList() {
        setMessage(listMessage, '', '');
        renderEmptyState('Loading media...');

        try {
            const result = await postUrlEncoded({
                action: 'getMediaList',
                csrf_token: csrfToken,
                media_type: currentFilters.media_type,
                status: currentFilters.status,
                folder: currentFilters.folder,
                search: currentFilters.search,
                limit: currentFilters.limit,
                offset: currentFilters.offset
            });

            if (result.status !== 'success') {
                renderEmptyState(result.message || 'Failed to load media.');
                setMessage(listMessage, 'error', result.message || 'Failed to load media.');
                updateCount(0);
                return;
            }

            const items = Array.isArray(result.items) ? result.items : [];
            updateCount(Number(result.count || items.length || 0));

            if (items.length === 0) {
                renderEmptyState('No media items found.');
                return;
            }

            mediaGrid.innerHTML = items.map(renderMediaCard).join('');
        } catch (error) {
            renderEmptyState('Unable to load media.');
            setMessage(listMessage, 'error', error.message || 'Unable to load media.');
            updateCount(0);
        }
    }

    function updateCount(count) {
        if (!mediaCount) {
            return;
        }

        mediaCount.textContent = count + (count === 1 ? ' item' : ' items');
    }

    function toggleUploadPanel(show) {
        if (!uploadPanel) {
            return;
        }

        uploadPanel.style.display = show ? 'block' : 'none';

        if (!show) {
            uploadForm?.reset();
            setMessage(uploadMessage, '', '');
        }
    }

    function openEditModal() {
        if (!editModal) {
            return;
        }

        editModal.style.display = 'block';
        document.body.classList.add('wi-modal-open');
    }

    function closeEditModal() {
        if (!editModal) {
            return;
        }

        editModal.style.display = 'none';
        document.body.classList.remove('wi-modal-open');

        if (editForm) {
            editForm.reset();
        }

        if (editPreview) {
            editPreview.innerHTML = '';
        }

        setMessage(editMessage, '', '');
    }

    function buildEditPreview(item) {
        const title = item.title || item.original_name || 'Untitled';

        if (isImage(item)) {
            return `
                <div class="wi-media-edit-preview-inner">
                    <div class="wi-media-edit-preview-image">
                        <img src="${escapeHtml(item.file_url)}" alt="${escapeHtml(item.alt_text || title)}">
                    </div>
                    <div class="wi-media-edit-preview-meta">
                        <div><strong>Name:</strong> ${escapeHtml(item.original_name || '—')}</div>
                        <div><strong>Type:</strong> ${escapeHtml(item.media_type || '—')}</div>
                        <div><strong>Size:</strong> ${formatBytes(item.file_size)}</div>
                        <div><strong>Folder:</strong> ${escapeHtml(item.folder || '—')}</div>
                    </div>
                </div>
            `;
        }

        return `
            <div class="wi-media-edit-preview-inner wi-media-edit-preview-file">
                <div class="wi-media-edit-preview-file-icon">${getFileIcon(item.media_type)}</div>
                <div class="wi-media-edit-preview-meta">
                    <div><strong>Name:</strong> ${escapeHtml(item.original_name || '—')}</div>
                    <div><strong>Type:</strong> ${escapeHtml(item.media_type || '—')}</div>
                    <div><strong>Size:</strong> ${formatBytes(item.file_size)}</div>
                    <div><strong>Folder:</strong> ${escapeHtml(item.folder || '—')}</div>
                    <div><strong>Uploaded:</strong> ${escapeHtml(formatDate(item.created_at))}</div>
                </div>
            </div>
        `;
    }

    async function loadMediaItem(id) {
        setMessage(editMessage, '', '');

        try {
            const result = await postUrlEncoded({
                action: 'getMediaItem',
                csrf_token: csrfToken,
                id: id
            });

            if (result.status !== 'success' || !result.item) {
                setMessage(listMessage, 'error', result.message || 'Failed to load media item.');
                return;
            }

            const item = result.item;

            if (editId) editId.value = item.id || '';
            if (editTitle) editTitle.value = item.title || '';
            if (editAltText) editAltText.value = item.alt_text || '';
            if (editFolder) editFolder.value = item.folder || '';
            if (editStatus) editStatus.value = String(item.status ?? '1');
            if (editPrivate) editPrivate.value = String(item.is_private ?? '0');
            if (editCaption) editCaption.value = item.caption || '';
            if (editDescription) editDescription.value = item.description || '';
            if (editPreview) editPreview.innerHTML = buildEditPreview(item);

            openEditModal();
        } catch (error) {
            setMessage(listMessage, 'error', error.message || 'Failed to load media item.');
        }
    }

    async function submitUploadForm(event) {
        event.preventDefault();

        if (!uploadForm) {
            return;
        }

        const fileInput = document.getElementById('wi-media-file');
        if (!fileInput || !fileInput.files || !fileInput.files.length) {
            setMessage(uploadMessage, 'error', 'Please select a file.');
            return;
        }

        setMessage(uploadMessage, '', '');
        uploadSubmitBtn.disabled = true;
        uploadSubmitBtn.textContent = 'Uploading...';

        try {
            const formData = new FormData(uploadForm);
            if (!formData.get('csrf_token')) {
                formData.append('csrf_token', csrfToken);
            }

            const result = await postFormData(formData);

            if (result.status !== 'success') {
                setMessage(uploadMessage, 'error', result.message || 'Upload failed.');
                return;
            }

            setMessage(uploadMessage, 'success', result.message || 'File uploaded successfully.');
            uploadForm.reset();
            await loadMediaList();
        } catch (error) {
            setMessage(uploadMessage, 'error', error.message || 'Upload failed.');
        } finally {
            uploadSubmitBtn.disabled = false;
            uploadSubmitBtn.textContent = 'Upload';
        }
    }

    async function submitEditForm(event) {
        event.preventDefault();

        if (!editForm) {
            return;
        }

        const id = editId?.value || '';
        if (!id) {
            setMessage(editMessage, 'error', 'Invalid media item.');
            return;
        }

        const formData = new FormData(editForm);

        try {
            const result = await postUrlEncoded({
                action: 'updateMedia',
                csrf_token: csrfToken,
                id: id,
                title: formData.get('title'),
                alt_text: formData.get('alt_text'),
                folder: formData.get('folder'),
                status: formData.get('status'),
                is_private: formData.get('is_private'),
                caption: formData.get('caption'),
                description: formData.get('description')
            });

            if (result.status !== 'success') {
                setMessage(editMessage, 'error', result.message || 'Failed to save changes.');
                return;
            }

            setMessage(editMessage, 'success', result.message || 'Media item updated successfully.');
            await loadMediaList();

            window.setTimeout(function () {
                closeEditModal();
            }, 500);
        } catch (error) {
            setMessage(editMessage, 'error', error.message || 'Failed to save changes.');
        }
    }

    async function deleteMediaItem(id) {
        const confirmed = window.confirm('Are you sure you want to delete this media item? This will remove the file and its database record.');
        if (!confirmed) {
            return;
        }

        setMessage(listMessage, '', '');

        try {
            const result = await postUrlEncoded({
                action: 'deleteMedia',
                csrf_token: csrfToken,
                id: id
            });

            if (result.status !== 'success') {
                setMessage(listMessage, 'error', result.message || 'Failed to delete media item.');
                return;
            }

            setMessage(listMessage, 'success', result.message || 'Media item deleted successfully.');
            await loadMediaList();
        } catch (error) {
            setMessage(listMessage, 'error', error.message || 'Failed to delete media item.');
        }
    }

    function handleGridClick(event) {
        const editBtn = event.target.closest('.wi-media-edit-btn');
        if (editBtn) {
            const id = Number(editBtn.getAttribute('data-id') || 0);
            if (id > 0) {
                loadMediaItem(id);
            }
            return;
        }

        const deleteBtn = event.target.closest('.wi-media-delete-btn');
        if (deleteBtn) {
            const id = Number(deleteBtn.getAttribute('data-id') || 0);
            if (id > 0) {
                deleteMediaItem(id);
            }
        }
    }

    function bindEvents() {
        openUploadBtn?.addEventListener('click', function () {
            toggleUploadPanel(true);
        });

        closeUploadBtn?.addEventListener('click', function () {
            toggleUploadPanel(false);
        });

        uploadForm?.addEventListener('submit', submitUploadForm);
        editForm?.addEventListener('submit', submitEditForm);

        applyFiltersBtn?.addEventListener('click', function () {
            collectFilters();
            loadMediaList();
        });

        resetFiltersBtn?.addEventListener('click', function () {
            resetFilters();
            loadMediaList();
        });

        searchInput?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                collectFilters();
                loadMediaList();
            }
        });

        mediaGrid?.addEventListener('click', handleGridClick);

        editCloseBtn?.addEventListener('click', closeEditModal);
        editCancelBtn?.addEventListener('click', closeEditModal);

        editModal?.addEventListener('click', function (event) {
            if (event.target.matches('[data-close-modal="1"]')) {
                closeEditModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && editModal && editModal.style.display === 'block') {
                closeEditModal();
            }
        });
    }

    bindEvents();
    loadMediaList();
})();