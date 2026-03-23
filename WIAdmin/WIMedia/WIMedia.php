<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Core Includes
|--------------------------------------------------------------------------
| Adjust only if your current admin bootstrap differs.
*/
require_once dirname(__DIR__, 2) . '/WIInc/WI_Start_Up.php';

/*
|--------------------------------------------------------------------------
| Basic Auth Guard
|--------------------------------------------------------------------------
| Replace with your proper WICMS admin auth check if needed.
*/
$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);

if (!$loggedIn) {
    header('Location: /');
    exit;
}

/*
|--------------------------------------------------------------------------
| Optional CSRF Token
|--------------------------------------------------------------------------
*/
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

/*
|--------------------------------------------------------------------------
| Ajax Path
|--------------------------------------------------------------------------
| Based on your likely structure:
| WIAdmin/WICore/WIAjax/WIMedia.php
*/
$mediaAjaxUrl = '/WIAdmin/WICore/WIAjax/WIMedia.php';
?>

<div class="wi-admin-media-page" id="wi-media-page" data-ajax-url="<?php echo htmlspecialchars($mediaAjaxUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="wi-page-header">
        <div class="wi-page-header-left">
            <h2 class="wi-page-title">Media Library</h2>
            <p class="wi-page-subtitle">Upload, manage and reuse media across WICMS.</p>
        </div>

        <div class="wi-page-header-right">
            <button type="button" class="wi-btn wi-btn-primary" id="wi-open-upload-panel">
                Upload Media
            </button>
        </div>
    </div>

    <div class="wi-media-toolbar">
        <div class="wi-media-toolbar-row">
            <div class="wi-media-filter-group">
                <label for="wi-media-search" class="wi-label">Search</label>
                <input type="text" id="wi-media-search" class="wi-input" placeholder="Search title, filename, caption..." />
            </div>

            <div class="wi-media-filter-group">
                <label for="wi-media-type-filter" class="wi-label">Type</label>
                <select id="wi-media-type-filter" class="wi-select">
                    <option value="">All Types</option>
                    <option value="image">Images</option>
                    <option value="video">Videos</option>
                    <option value="audio">Audio</option>
                    <option value="document">Documents</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="wi-media-filter-group">
                <label for="wi-media-status-filter" class="wi-label">Status</label>
                <select id="wi-media-status-filter" class="wi-select">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="wi-media-filter-group">
                <label for="wi-media-folder-filter" class="wi-label">Folder</label>
                <input type="text" id="wi-media-folder-filter" class="wi-input" placeholder="Folder name" />
            </div>

            <div class="wi-media-filter-actions">
                <button type="button" class="wi-btn wi-btn-secondary" id="wi-media-apply-filters">Apply</button>
                <button type="button" class="wi-btn wi-btn-light" id="wi-media-reset-filters">Reset</button>
            </div>
        </div>
    </div>

    <div class="wi-media-upload-panel" id="wi-media-upload-panel" style="display:none;">
        <div class="wi-card">
            <div class="wi-card-header">
                <h3>Upload Media</h3>
            </div>

            <div class="wi-card-body">
                <form id="wi-media-upload-form" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="action" value="uploadMedia">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="wi-form-row">
                        <div class="wi-form-group wi-form-group-full">
                            <label for="wi-media-file" class="wi-label">Select File</label>
                            <input type="file" name="file" id="wi-media-file" class="wi-input-file" required>
                            <small class="wi-help-text">
                                Allowed: jpg, jpeg, png, gif, webp, pdf, doc, docx, xls, xlsx, txt, csv, mp3, wav, ogg, mp4, webm, ogv
                            </small>
                        </div>
                    </div>

                    <div class="wi-form-row">
                        <div class="wi-form-group">
                            <label for="wi-media-title" class="wi-label">Title</label>
                            <input type="text" name="title" id="wi-media-title" class="wi-input">
                        </div>

                        <div class="wi-form-group">
                            <label for="wi-media-alt-text" class="wi-label">Alt Text</label>
                            <input type="text" name="alt_text" id="wi-media-alt-text" class="wi-input">
                        </div>
                    </div>

                    <div class="wi-form-row">
                        <div class="wi-form-group">
                            <label for="wi-media-folder" class="wi-label">Folder</label>
                            <input type="text" name="folder" id="wi-media-folder" class="wi-input">
                        </div>

                        <div class="wi-form-group">
                            <label for="wi-media-status" class="wi-label">Status</label>
                            <select name="status" id="wi-media-status" class="wi-select">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="wi-form-group">
                            <label for="wi-media-private" class="wi-label">Private</label>
                            <select name="is_private" id="wi-media-private" class="wi-select">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="wi-form-row">
                        <div class="wi-form-group wi-form-group-full">
                            <label for="wi-media-caption" class="wi-label">Caption</label>
                            <textarea name="caption" id="wi-media-caption" class="wi-textarea" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="wi-form-row">
                        <div class="wi-form-group wi-form-group-full">
                            <label for="wi-media-description" class="wi-label">Description</label>
                            <textarea name="description" id="wi-media-description" class="wi-textarea" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="wi-form-actions">
                        <button type="submit" class="wi-btn wi-btn-primary" id="wi-media-upload-submit">Upload</button>
                        <button type="button" class="wi-btn wi-btn-light" id="wi-close-upload-panel">Cancel</button>
                    </div>
                </form>

                <div id="wi-media-upload-message" class="wi-message-area"></div>
            </div>
        </div>
    </div>

    <div class="wi-media-content">
        <div class="wi-media-list-header">
            <h3>Media Items</h3>
            <span class="wi-media-count" id="wi-media-count">0 items</span>
        </div>

        <div id="wi-media-list-message" class="wi-message-area"></div>

        <div class="wi-media-grid" id="wi-media-grid">
            <div class="wi-media-empty">Loading media...</div>
        </div>
    </div>
</div>

<div class="wi-modal" id="wi-media-edit-modal" style="display:none;">
    <div class="wi-modal-overlay" data-close-modal="1"></div>

    <div class="wi-modal-dialog wi-modal-lg">
        <div class="wi-modal-header">
            <h3>Edit Media</h3>
            <button type="button" class="wi-modal-close" id="wi-media-edit-close" aria-label="Close">&times;</button>
        </div>

        <div class="wi-modal-body">
            <form id="wi-media-edit-form" novalidate>
                <input type="hidden" name="action" value="updateMedia">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id" id="wi-edit-id">

                <div class="wi-media-edit-preview" id="wi-media-edit-preview"></div>

                <div class="wi-form-row">
                    <div class="wi-form-group">
                        <label for="wi-edit-title" class="wi-label">Title</label>
                        <input type="text" name="title" id="wi-edit-title" class="wi-input">
                    </div>

                    <div class="wi-form-group">
                        <label for="wi-edit-alt-text" class="wi-label">Alt Text</label>
                        <input type="text" name="alt_text" id="wi-edit-alt-text" class="wi-input">
                    </div>
                </div>

                <div class="wi-form-row">
                    <div class="wi-form-group">
                        <label for="wi-edit-folder" class="wi-label">Folder</label>
                        <input type="text" name="folder" id="wi-edit-folder" class="wi-input">
                    </div>

                    <div class="wi-form-group">
                        <label for="wi-edit-status" class="wi-label">Status</label>
                        <select name="status" id="wi-edit-status" class="wi-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="wi-form-group">
                        <label for="wi-edit-private" class="wi-label">Private</label>
                        <select name="is_private" id="wi-edit-private" class="wi-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                <div class="wi-form-row">
                    <div class="wi-form-group wi-form-group-full">
                        <label for="wi-edit-caption" class="wi-label">Caption</label>
                        <textarea name="caption" id="wi-edit-caption" class="wi-textarea" rows="3"></textarea>
                    </div>
                </div>

                <div class="wi-form-row">
                    <div class="wi-form-group wi-form-group-full">
                        <label for="wi-edit-description" class="wi-label">Description</label>
                        <textarea name="description" id="wi-edit-description" class="wi-textarea" rows="4"></textarea>
                    </div>
                </div>

                <div class="wi-form-actions">
                    <button type="submit" class="wi-btn wi-btn-primary">Save Changes</button>
                    <button type="button" class="wi-btn wi-btn-light" id="wi-media-edit-cancel">Cancel</button>
                </div>
            </form>

            <div id="wi-media-edit-message" class="wi-message-area"></div>
        </div>
    </div>
</div>

<script>
window.WIMediaConfig = {
    ajaxUrl: <?php echo json_encode($mediaAjaxUrl, JSON_UNESCAPED_SLASHES); ?>,
    csrfToken: <?php echo json_encode($csrfToken); ?>
};
</script>
<script src="/WIAdmin/WIMedia/js/media.js"></script>