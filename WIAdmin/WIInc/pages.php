<?php
$pageObj = new WIPage();
$pages = $pageObj->getPages();
?>

<aside class="right-side">
    <?php echo WIToken::csrfField('wi_ajax'); ?>

    <div class="wi-admin-header">
        <h2>Pages</h2>
        <p class="text-muted">Manage page layout sections and content modules for each page.</p>
    </div>

    <section class="content wi-pages-shell">
        <div class="wi-admin-panel">

            <div class="wi-section-head">
                <div>
                    <h3>Page Manager</h3>
                    <p>Control page structure, enabled sections and main content modules.</p>
                </div>

                <button type="button" class="btn btn-primary wi-btn-primary" onclick="WIPages.openCreateModal();">
                    Add Page
                </button>
            </div>

            <div class="wi-pages-table-wrap">
                <table class="table table-striped wi-pages-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contents</th>
                            <th>Panel</th>
                            <th>Top Head</th>
                            <th>Header</th>
                            <th>Left Sidebar</th>
                            <th>Right Sidebar</th>
                            <th>Footer</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr id="page-row-<?php echo (int)$page['id']; ?>">
                            <td><?php echo (int)$page['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars((string)$page['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong></td>
                            <td><code><?php echo htmlspecialchars((string)$page['contents'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></code></td>
                            <td><?php echo (string)$page['panel'] === '1' ? 'On' : 'Off'; ?></td>
                            <td><?php echo (string)$page['top_head'] === '1' ? 'On' : 'Off'; ?></td>
                            <td><?php echo (string)$page['header'] === '1' ? 'On' : 'Off'; ?></td>
                            <td><?php echo (string)$page['left_sidebar'] === '1' ? 'On' : 'Off'; ?></td>
                            <td><?php echo (string)$page['right_sidebar'] === '1' ? 'On' : 'Off'; ?></td>
                            <td><?php echo (string)$page['footer'] === '1' ? 'On' : 'Off'; ?></td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-default btn-sm"
                                    onclick='WIPages.openEditModal(<?php echo json_encode([
                                        "id" => (int)$page["id"],
                                        "name" => (string)$page["name"],
                                        "panel" => (string)$page["panel"],
                                        "top_head" => (string)$page["top_head"],
                                        "header" => (string)$page["header"],
                                        "left_sidebar" => (string)$page["left_sidebar"],
                                        "right_sidebar" => (string)$page["right_sidebar"],
                                        "contents" => (string)$page["contents"],
                                        "footer" => (string)$page["footer"]
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="WIPages.deletePage(<?php echo (int)$page['id']; ?>);">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <div id="wi-page-modal" class="wi-page-modal hide">
        <div class="wi-page-modal-dialog">
            <div class="wi-page-modal-header">
                <h3 id="wi-page-modal-title">Add Page</h3>
                <button type="button" class="wi-modal-close" onclick="WIPages.closeModal();">&times;</button>
            </div>

            <div class="wi-page-modal-body">
                <input type="hidden" id="page-id" value="0">

                <div class="form-group">
                    <label for="page-name">Page Name</label>
                    <input type="text" id="page-name" class="form-control" placeholder="about_us">
                </div>

                <div class="form-group">
                    <label for="page-contents">Contents Module</label>
                    <input type="text" id="page-contents" class="form-control" placeholder="about_us">
                </div>

                <div class="wi-page-grid">
                    <div class="form-group">
                        <label for="page-panel">Panel</label>
                        <select id="page-panel" class="form-control">
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="page-top-head">Top Head</label>
                        <select id="page-top-head" class="form-control">
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="page-header">Header</label>
                        <select id="page-header" class="form-control">
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="page-left-sidebar">Left Sidebar</label>
                        <select id="page-left-sidebar" class="form-control">
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="page-right-sidebar">Right Sidebar</label>
                        <select id="page-right-sidebar" class="form-control">
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="page-footer">Footer</label>
                        <select id="page-footer" class="form-control">
                            <option value="0">Off</option>
                            <option value="1">On</option>
                        </select>
                    </div>
                </div>

                <div id="page-results"></div>
            </div>

            <div class="wi-page-modal-footer">
                <button type="button" class="btn btn-default" onclick="WIPages.closeModal();">Cancel</button>
                <button type="button" class="btn btn-primary" id="page-save-btn" onclick="WIPages.savePage();">Save Page</button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIPages.js"></script>
</aside>