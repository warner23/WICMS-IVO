<?php
declare(strict_types=1);

/**
 * FILE:
 * /WIAdmin/WIInc/media.php
 *
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WIKitchenCompli
 * Page: WIMedia Centre
 * Type: Admin View
 * Layer: Admin UI
 * Version: 1.0.0
 * Status: Active
 *
 * Purpose:
 * - Renders the WIMedia Centre inside the standard WIAdmin layout.
 * - Uses the correct WIAdmin wrapper:
 *   aside.right-side > section.content-header > section.content
 * - Does not perform upload logic.
 * - Does not perform DB logic.
 * - Calls WIMedia only through /WIAdmin/WICore/WIJ/WIMedia.js.
 *
 * Architecture Rules:
 * - UI only.
 * - No direct DB calls.
 * - No direct upload handling.
 * - No WIModal usage in this page.
 * - WIMedia backend is reached through WIAjax.php → WIAjaxMedia.php → WIMedia.php.
 */

?>

<link rel="stylesheet" href="WICore/WICSS/WIMedia.css">

<aside class="right-side">
    <section class="content-header">
        <h1>
            Media Centre
            <small>WIMedia</small>
        </h1>

        <ol class="breadcrumb">
            <li>
                <a href="dashboard.php">
                    <i class="fa fa-dashboard"></i> Home
                </a>
            </li>
            <li class="active">Media Centre</li>
        </ol>
    </section>

    <section class="content wi-media-page">
        <div id="wiMediaCentre" class="wi-media-centre" data-wi-media-centre>
            <header class="wi-media-header">
                <div>
                    <h2>Media Centre</h2>
                    <p>
                        Upload, browse, view, download and manage files through the shared WIMedia system.
                    </p>
                </div>

                <div class="wi-media-header-actions">
                    <button type="button" class="wi-media-btn" data-media-refresh>
                        Refresh
                    </button>
                </div>
            </header>

            <section class="wi-media-overview-grid" aria-label="WIMedia summary">
                <article class="wi-media-summary-card">
                    <strong>WIMedia</strong>
                    <span>Shared media, documents and evidence</span>
                </article>

                <article class="wi-media-summary-card">
                    <strong>Storage</strong>
                    <span>WIAdmin/WIMedia/</span>
                </article>

                <article class="wi-media-summary-card">
                    <strong>Links</strong>
                    <span>wi_media_links</span>
                </article>

                <article class="wi-media-summary-card">
                    <strong>Events</strong>
                    <span>wi_media_events</span>
                </article>
            </section>

            <nav class="wi-media-tabs" data-media-tabs aria-label="WIMedia sections">
                <button type="button" class="is-active" data-media-tab="uploads">
                    Uploads
                </button>
                <button type="button" data-media-tab="documents">
                    Documents
                </button>
                <button type="button" data-media-tab="media">
                    Media
                </button>
                <button type="button" data-media-tab="images">
                    Images
                </button>
                <button type="button" data-media-tab="linked">
                    Linked / Evidence
                </button>
            </nav>

            <section class="wi-media-message" data-media-message hidden></section>

            <section class="wi-media-tab-panel is-active" data-media-tab-panel="uploads">
                <section class="wi-media-admin-layout">
                    <aside class="wi-media-panel wi-media-upload-panel">
                        <h3>Upload</h3>

                        <form id="wiMediaUploadForm" data-media-upload-form enctype="multipart/form-data">
                            <label class="wi-media-field">
                                <span>File</span>
                                <input type="file" name="file" data-media-file>
                            </label>

                            <label class="wi-media-field">
                                <span>Title</span>
                                <input type="text" name="title" data-media-title placeholder="Optional title">
                            </label>

                            <label class="wi-media-field">
                                <span>Folder</span>
                                <input type="text" name="folder" data-media-folder placeholder="e.g. wicos-documents">
                            </label>

                            <label class="wi-media-field">
                                <span>System code</span>
                                <select name="system_code" data-media-system-code>
                                    <option value="wicms">WICMS</option>
                                    <option value="wicos">WICOS</option>
                                    <option value="wihr">WIHR</option>
                                    <option value="wiorg">WIOrg</option>
                                    <option value="wilabs">WILabs</option>
                                </select>
                            </label>

                            <label class="wi-media-field">
                                <span>Entity type</span>
                                <input type="text" name="entity_type" data-media-entity-type placeholder="Optional e.g. document">
                            </label>

                            <label class="wi-media-field">
                                <span>Entity ID</span>
                                <input type="number" name="entity_id" data-media-entity-id min="0" placeholder="Optional">
                            </label>

                            <label class="wi-media-field">
                                <span>Link type</span>
                                <input type="text" name="link_type" data-media-link-type placeholder="Optional e.g. document_file">
                            </label>

                            <label class="wi-media-field">
                                <span>Business ID</span>
                                <input type="number" name="org_business_id" data-media-business-id min="0" placeholder="Optional">
                            </label>

                            <label class="wi-media-field">
                                <span>Site ID</span>
                                <input type="number" name="org_site_id" data-media-site-id min="0" placeholder="Optional">
                            </label>

                            <label class="wi-media-field">
                                <span>Department ID</span>
                                <input type="number" name="org_department_id" data-media-department-id min="0" placeholder="Optional">
                            </label>

                            <label class="wi-media-field">
                                <span>Visibility</span>
                                <select name="visibility" data-media-visibility>
                                    <option value="private">Private</option>
                                    <option value="restricted">Restricted</option>
                                    <option value="public">Public</option>
                                </select>
                            </label>

                            <label class="wi-media-check">
                                <input type="checkbox" name="is_sensitive" value="1" data-media-sensitive>
                                <span>Mark as sensitive</span>
                            </label>

                            <button type="submit" class="wi-media-btn wi-media-btn-primary">
                                Upload File
                            </button>
                        </form>
                    </aside>

                    <section class="wi-media-panel wi-media-library-panel">
                        <div class="wi-media-toolbar">
                            <label>
                                <span>Type</span>
                                <select data-media-filter-type>
                                    <option value="">All</option>
                                    <option value="image">Images</option>
                                    <option value="document">Documents</option>
                                    <option value="video">Videos</option>
                                    <option value="audio">Audio</option>
                                    <option value="archive">Archives</option>
                                    <option value="other">Other</option>
                                </select>
                            </label>

                            <label>
                                <span>Search</span>
                                <input type="search" data-media-filter-search placeholder="Search media">
                            </label>

                            <label>
                                <span>Business</span>
                                <input type="number" data-media-filter-business min="0" placeholder="All">
                            </label>

                            <label>
                                <span>Site</span>
                                <input type="number" data-media-filter-site min="0" placeholder="All">
                            </label>

                            <label>
                                <span>Department</span>
                                <input type="number" data-media-filter-department min="0" placeholder="All">
                            </label>

                            <button type="button" class="wi-media-btn" data-media-apply-filters>
                                Apply
                            </button>
                        </div>

                        <div class="wi-media-stats" data-media-stats>
                            Loading media...
                        </div>

                        <div class="wi-media-list" data-media-list></div>
                    </section>
                </section>
            </section>

            <section class="wi-media-tab-panel" data-media-tab-panel="documents">
                <section class="wi-media-panel">
                    <div class="wi-media-panel-head">
                        <div>
                            <h3>Documents</h3>
                            <p>PDF, Word, Excel, CSV, PowerPoint and other safe document files.</p>
                        </div>

                        <button type="button" class="wi-media-btn" data-media-load-type="document">
                            Refresh Documents
                        </button>
                    </div>

                    <div class="wi-media-document-list" data-media-document-list></div>
                </section>
            </section>

            <section class="wi-media-tab-panel" data-media-tab-panel="media">
                <section class="wi-media-preview-layout">
                    <section class="wi-media-panel wi-media-preview-panel">
                        <h3>Player</h3>

                        <div class="wi-media-player-box" data-media-player>
                            <div class="wi-media-player-empty">
                                Select a video or audio file to preview it here.
                            </div>
                        </div>
                    </section>

                    <aside class="wi-media-panel wi-media-side-panel">
                        <div class="wi-media-panel-head">
                            <div>
                                <h3>Video & Audio</h3>
                                <p>Uploaded video and audio media.</p>
                            </div>

                            <button type="button" class="wi-media-btn" data-media-load-type="video_audio">
                                Refresh
                            </button>
                        </div>

                        <div class="wi-media-side-list" data-media-video-audio-list></div>
                    </aside>
                </section>
            </section>

            <section class="wi-media-tab-panel" data-media-tab-panel="images">
                <section class="wi-media-preview-layout">
                    <section class="wi-media-panel wi-media-preview-panel">
                        <h3>Image Preview</h3>

                        <div class="wi-media-image-preview" data-media-image-preview>
                            <div class="wi-media-player-empty">
                                Select an image to preview it here.
                            </div>
                        </div>
                    </section>

                    <aside class="wi-media-panel wi-media-side-panel">
                        <div class="wi-media-panel-head">
                            <div>
                                <h3>Images</h3>
                                <p>Uploaded images shown in a scalable grid.</p>
                            </div>

                            <button type="button" class="wi-media-btn" data-media-load-type="image">
                                Refresh
                            </button>
                        </div>

                        <div class="wi-media-image-grid" data-media-image-grid></div>
                    </aside>
                </section>
            </section>

            <section class="wi-media-tab-panel" data-media-tab-panel="linked">
                <section class="wi-media-panel">
                    <div class="wi-media-panel-head">
                        <div>
                            <h3>Linked / Evidence</h3>
                            <p>
                                View files linked to a specific system record, such as a document,
                                checklist answer, incident, legal requirement or training record.
                            </p>
                        </div>
                    </div>

                    <div class="wi-media-linked-filters">
                        <label>
                            <span>System</span>
                            <select data-linked-system-code>
                                <option value="wicos">WICOS</option>
                                <option value="wicms">WICMS</option>
                                <option value="wihr">WIHR</option>
                                <option value="wiorg">WIOrg</option>
                                <option value="wilabs">WILabs</option>
                            </select>
                        </label>

                        <label>
                            <span>Entity Type</span>
                            <input type="text" data-linked-entity-type placeholder="e.g. document">
                        </label>

                        <label>
                            <span>Entity ID</span>
                            <input type="number" data-linked-entity-id min="0">
                        </label>

                        <label>
                            <span>Link Type</span>
                            <input type="text" data-linked-link-type placeholder="Optional e.g. document_file">
                        </label>

                        <button type="button" class="wi-media-btn wi-media-btn-primary" data-media-load-linked>
                            Load Linked Media
                        </button>
                    </div>

                    <div class="wi-media-linked-list" data-media-linked-list></div>
                </section>
            </section>
        </div>
    </section>
</aside>

<script>
    window.WI_AJAX_URL = 'WICore/WIClass/WIAjax.php';
    window.WI_MEDIA_ENDPOINT = 'WICore/WIClass/WIAjax.php';
    window.WI_MEDIA_DEBUG = false;
</script>
<script src="WICore/WIJ/WIMedia.js"></script>