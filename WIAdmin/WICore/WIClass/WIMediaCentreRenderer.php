<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WIKitchenCompli / WICOS
| File: WIMediaCentreRenderer.php
| Location: /root/WIAdmin/WICore/WIClass/WIMediaCentreRenderer.php
| Type: PHP UI Renderer
| Layer: Shared UI Support
| Purpose Area: Media Centre Page Renderer
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Batch 1 Refactor
|--------------------------------------------------------------------------
| Summary:
| Full Media Centre renderer for admin page usage.
| - Renders the full WIMedia centre shell only
| - Provides drag/drop upload, filters, tabs, lists and preview containers
| - Uses hooks consumed by WIMedia.js and WIMediaCenter.js
| - Does not render modal bodies; use WIMediaComponentRenderer for small fragments
| - Contains no upload, database, permission or compliance business logic
|--------------------------------------------------------------------------
*/

class WIMediaCentreRenderer
{
    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    /**
     * Renders the full tabbed Media Centre shell.
     *
     * @param array<string, mixed> $context Optional media context.
     *
     * @return string
     */
    public function render(array $context = []): string
    {
        $contextJson = $this->contextJson($context);

        return <<<HTML
<div
    id="wiMediaCentre"
    class="wi-media-centre"
    data-wimedia-centre
    data-wi-media-centre
    data-context="{$contextJson}"
>
    <header class="wi-media-header">
        <div>
            <h2>Media Centre</h2>
            <p>Upload, browse, preview, download and manage files through the shared WIMedia system.</p>
        </div>

        <div class="wi-media-header-actions">
            <button type="button" class="wi-media-btn" data-wimedia-action="refresh" data-media-refresh>
                Refresh
            </button>
        </div>
    </header>

    <section class="wi-media-message" data-wimedia-message data-media-message hidden></section>

    <section class="wi-media-upload-hero" data-wimedia-dropzone data-media-dropzone>
        <form class="wi-media-upload-form" data-wimedia-upload-form data-media-upload-form enctype="multipart/form-data">
            <div class="wi-media-dropzone">
                <div class="wi-media-dropzone-icon" aria-hidden="true">
                    <i class="fa fa-cloud-upload"></i>
                </div>

                <div class="wi-media-dropzone-copy">
                    <h3>Drop files here to upload</h3>
                    <p>Drag and drop multiple files, or click this area to choose files from your device.</p>
                    <span data-wimedia-selected-files data-media-selected-files>No files selected.</span>
                </div>

                <input
                    type="file"
                    name="files[]"
                    multiple
                    data-wimedia-file-input
                    data-media-file
                    data-wimedia-upload-input
                    aria-label="Choose files to upload"
                >
            </div>

            <div class="wi-media-upload-options">
                <label class="wi-media-field">
                    <span>System</span>
                    <select name="system_code" data-media-system-code data-wimedia-filter="system_code">
                        <option value="wicos">WICOS</option>
                        <option value="wicms">WICMS</option>
                        <option value="wihr">WIHR</option>
                        <option value="wiorg">WIOrg</option>
                        <option value="wilabs">WILabs</option>
                    </select>
                </label>

                <label class="wi-media-field">
                    <span>Folder</span>
                    <input type="text" name="folder" data-media-folder placeholder="Optional folder">
                </label>

                <label class="wi-media-field">
                    <span>Title prefix</span>
                    <input type="text" name="title" data-media-title placeholder="Optional title or batch label">
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
                    Upload selected files
                </button>
            </div>

            <details class="wi-media-advanced-options">
                <summary>Advanced link/context options</summary>

                <div class="wi-media-advanced-grid">
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
                        <input type="text" name="link_type" data-media-link-type placeholder="Optional e.g. evidence">
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
                </div>
            </details>

            <div class="wi-media-upload-progress" data-wimedia-upload-progress data-media-upload-progress>
                Ready.
            </div>
        </form>
    </section>

    <nav class="wi-media-tabs" data-media-tabs aria-label="WIMedia sections">
        <button type="button" class="is-active" data-wimedia-tab="uploads" data-media-tab="uploads">
            Uploads
        </button>
        <button type="button" data-wimedia-tab="documents" data-media-tab="documents">
            Documents
        </button>
        <button type="button" data-wimedia-tab="media" data-media-tab="media">
            Media
        </button>
        <button type="button" data-wimedia-tab="images" data-media-tab="images">
            Images
        </button>
        <button type="button" data-wimedia-tab="linked" data-media-tab="linked">
            Linked / Evidence
        </button>
    </nav>

    <section class="wi-media-tab-panel is-active" data-wimedia-panel="uploads" data-media-tab-panel="uploads">
        <section class="wi-media-panel wi-media-library-panel">
            <div class="wi-media-panel-head">
                <div>
                    <h3>Media Library</h3>
                    <p>Browse uploaded files. The page displays media only; WIMedia services own the rules.</p>
                </div>

                <button type="button" class="wi-media-btn" data-wimedia-action="refresh" data-media-refresh>
                    Refresh Library
                </button>
            </div>

            <div class="wi-media-toolbar">
                <label>
                    <span>Type</span>
                    <select data-media-filter-type data-wimedia-filter="media_type">
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
                    <input type="search" data-media-filter-search data-wimedia-filter="search" placeholder="Search media">
                </label>

                <label>
                    <span>Business</span>
                    <input type="number" data-media-filter-business data-wimedia-filter="org_business_id" min="0" placeholder="All">
                </label>

                <label>
                    <span>Site</span>
                    <input type="number" data-media-filter-site data-wimedia-filter="org_site_id" min="0" placeholder="All">
                </label>

                <label>
                    <span>Department</span>
                    <input type="number" data-media-filter-department data-wimedia-filter="org_department_id" min="0" placeholder="All">
                </label>

                <button type="button" class="wi-media-btn" data-media-apply-filters data-wimedia-apply-filters>
                    Apply
                </button>
            </div>

            <div class="wi-media-stats" data-wimedia-stats data-media-stats>
                Loading media...
            </div>

            <div class="wi-media-list" data-wimedia-list="all" data-media-list></div>
        </section>
    </section>

    <section class="wi-media-tab-panel" data-wimedia-panel="documents" data-media-tab-panel="documents">
        <section class="wi-media-panel">
            <div class="wi-media-panel-head">
                <div>
                    <h3>Documents</h3>
                    <p>PDF, Word, Excel, CSV, PowerPoint and other safe document files.</p>
                </div>

                <button type="button" class="wi-media-btn" data-wimedia-load-type="document" data-media-load-type="document">
                    Refresh Documents
                </button>
            </div>

            <div class="wi-media-document-list" data-wimedia-list="documents" data-media-document-list></div>
        </section>
    </section>

    <section class="wi-media-tab-panel" data-wimedia-panel="media" data-media-tab-panel="media">
        <section class="wi-media-preview-layout">
            <section class="wi-media-panel wi-media-preview-panel">
                <h3>Player</h3>

                <div class="wi-media-player-box" data-wimedia-player-stage data-media-player>
                    <div class="wi-media-player-empty">Select a video or audio file to preview it here.</div>
                </div>

                <div class="wi-media-player-actions">
                    <button type="button" class="wi-media-btn" data-wimedia-player-prev data-media-player-prev>
                        Previous
                    </button>
                    <button type="button" class="wi-media-btn" data-wimedia-player-next data-media-player-next>
                        Next
                    </button>
                </div>
            </section>

            <aside class="wi-media-panel wi-media-side-panel">
                <div class="wi-media-panel-head">
                    <div>
                        <h3>Video & Audio</h3>
                        <p>Uploaded video and audio media.</p>
                    </div>

                    <button type="button" class="wi-media-btn" data-wimedia-load-type="video_audio" data-media-load-type="video_audio">
                        Refresh
                    </button>
                </div>

                <div class="wi-media-side-list" data-wimedia-list="media" data-media-video-audio-list></div>
            </aside>
        </section>
    </section>

    <section class="wi-media-tab-panel" data-wimedia-panel="images" data-media-tab-panel="images">
        <section class="wi-media-preview-layout">
            <section class="wi-media-panel wi-media-preview-panel">
                <h3>Image Preview</h3>

                <div class="wi-media-image-preview" data-wimedia-image-preview data-media-image-preview>
                    <div class="wi-media-player-empty">Select an image to preview it here.</div>
                </div>
            </section>

            <aside class="wi-media-panel wi-media-side-panel">
                <div class="wi-media-panel-head">
                    <div>
                        <h3>Images</h3>
                        <p>Uploaded images shown in a scalable grid.</p>
                    </div>

                    <button type="button" class="wi-media-btn" data-wimedia-load-type="image" data-media-load-type="image">
                        Refresh
                    </button>
                </div>

                <div class="wi-media-image-grid" data-wimedia-list="images" data-media-image-grid></div>
            </aside>
        </section>
    </section>

    <section class="wi-media-tab-panel" data-wimedia-panel="linked" data-media-tab-panel="linked">
        <section class="wi-media-panel">
            <div class="wi-media-panel-head">
                <div>
                    <h3>Linked / Evidence</h3>
                    <p>View files linked to a specific system record.</p>
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

                <button type="button" class="wi-media-btn wi-media-btn-primary" data-wimedia-load-linked data-media-load-linked>
                    Load Linked Media
                </button>
            </div>

            <div class="wi-media-linked-list" data-wimedia-list="linked" data-media-linked-list></div>
        </section>
    </section>
</div>
HTML;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Encodes context for safe HTML attribute usage.
     *
     * @param array<string, mixed> $context Media context.
     *
     * @return string
     */
    private function contextJson(array $context): string
    {
        return htmlspecialchars(
            json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}',
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
