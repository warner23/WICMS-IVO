<?php
declare(strict_types=1);

/**
 * WIBlog Options Page
 * Location: WIPlugin/WIBlog/WIInc/site/WIBlog/blog_options.php
 */

$manifest = [];
if (class_exists('WIBlog')) {
    try {
        $blogPlugin = new WIBlog();
        $manifest = $blogPlugin->getManifest();
    } catch (\Throwable $e) {
        $manifest = [];
    }
}

$options = new WIBlog_Options($manifest);
$options->initialiseDefaults();

$general     = $options->generalOptions();
$design      = $options->designOptions();
$content     = $options->contentOptions();
$seo         = $options->seoOptions();
$performance = $options->performanceOptions();
$business    = $options->businessOptions();
$permissions = $options->permissionOptions();

function wiblog_checked(string $value): string
{
    return $value === '1' ? 'checked' : '';
}

function wiblog_value(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<style>
.wiblog-options-page {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
}

.wiblog-option-card {
    background: #ffffff;
    border: 1px solid #e6edf3;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,.04);
    overflow: hidden;
}

.wiblog-option-card .card-head {
    background: #f8fbfd;
    border-bottom: 1px solid #e6edf3;
    padding: 16px 18px;
}

.wiblog-option-card .card-head h3 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 700;
    color: #22313f;
}

.wiblog-option-card .card-head p {
    margin: 0;
    font-size: 13px;
    color: #627d98;
}

.wiblog-option-card .card-body {
    padding: 18px;
}

.wiblog-option-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px 18px;
}

.wiblog-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.wiblog-field label {
    font-weight: 600;
    color: #334e68;
    font-size: 13px;
}

.wiblog-field input[type="text"],
.wiblog-field input[type="number"],
.wiblog-field input[type="url"],
.wiblog-field input[type="color"],
.wiblog-field select,
.wiblog-field textarea {
    width: 100%;
    border: 1px solid #cfd9e3;
    border-radius: 10px;
    padding: 10px 12px;
    background: #fff;
    color: #243b53;
    font-size: 14px;
    transition: all .2s ease;
}

.wiblog-field textarea {
    min-height: 100px;
    resize: vertical;
}

.wiblog-field input:focus,
.wiblog-field select:focus,
.wiblog-field textarea:focus {
    outline: none;
    border-color: #2f6ea3;
    box-shadow: 0 0 0 3px rgba(47,110,163,.12);
}

.wiblog-switch {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 42px;
    padding-top: 22px;
}

.wiblog-switch input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

.wiblog-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
    margin-top: 12px;
}

.wiblog-actions .btn {
    border-radius: 10px;
    padding: 10px 16px;
    font-weight: 600;
}

.wiblog-inline-note {
    font-size: 12px;
    color: #829ab1;
}

@media (max-width: 991px) {
    .wiblog-option-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="wiblog-options-page">

    <form method="post" action="" id="wiblog-options-form">

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>General</h3>
                <p>Core blog identity, paging and localisation settings.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-field">
                        <label for="blog_name">Blog Name</label>
                        <input type="text" id="blog_name" name="blog_name" value="<?php echo wiblog_value($general['blog_name']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="blog_subtitle">Blog Subtitle</label>
                        <input type="text" id="blog_subtitle" name="blog_subtitle" value="<?php echo wiblog_value($general['blog_subtitle']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="default_language">Default Language</label>
                        <input type="text" id="default_language" name="default_language" value="<?php echo wiblog_value($general['default_language']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="timezone">Timezone</label>
                        <input type="text" id="timezone" name="timezone" value="<?php echo wiblog_value($general['timezone']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="date_format">Date Format</label>
                        <input type="text" id="date_format" name="date_format" value="<?php echo wiblog_value($general['date_format']); ?>">
                        <span class="wiblog-inline-note">Example: F j, Y</span>
                    </div>

                    <div class="wiblog-field">
                        <label for="posts_per_page">Posts Per Page</label>
                        <input type="number" id="posts_per_page" name="posts_per_page" value="<?php echo wiblog_value($general['posts_per_page']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="excerpt_length">Excerpt Length</label>
                        <input type="number" id="excerpt_length" name="excerpt_length" value="<?php echo wiblog_value($general['excerpt_length']); ?>">
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>Design</h3>
                <p>Layout, colour scheme, visual style and front-end identity.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-field">
                        <label for="layout_style">Layout Style</label>
                        <select id="layout_style" name="layout_style">
                            <option value="grid" <?php echo $design['layout_style'] === 'grid' ? 'selected' : ''; ?>>Grid</option>
                            <option value="list" <?php echo $design['layout_style'] === 'list' ? 'selected' : ''; ?>>List</option>
                            <option value="magazine" <?php echo $design['layout_style'] === 'magazine' ? 'selected' : ''; ?>>Magazine</option>
                        </select>
                    </div>

                    <div class="wiblog-field">
                        <label for="card_style">Card Style</label>
                        <select id="card_style" name="card_style">
                            <option value="modern" <?php echo $design['card_style'] === 'modern' ? 'selected' : ''; ?>>Modern</option>
                            <option value="classic" <?php echo $design['card_style'] === 'classic' ? 'selected' : ''; ?>>Classic</option>
                            <option value="minimal" <?php echo $design['card_style'] === 'minimal' ? 'selected' : ''; ?>>Minimal</option>
                        </select>
                    </div>

                    <div class="wiblog-field">
                        <label for="accent_color">Accent Colour</label>
                        <input type="color" id="accent_color" name="accent_color" value="<?php echo wiblog_value($design['accent_color']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="secondary_color">Secondary Colour</label>
                        <input type="color" id="secondary_color" name="secondary_color" value="<?php echo wiblog_value($design['secondary_color']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="background_color">Background Colour</label>
                        <input type="color" id="background_color" name="background_color" value="<?php echo wiblog_value($design['background_color']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="text_color">Text Colour</label>
                        <input type="color" id="text_color" name="text_color" value="<?php echo wiblog_value($design['text_color']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="border_radius">Border Radius</label>
                        <input type="number" id="border_radius" name="border_radius" value="<?php echo wiblog_value($design['border_radius']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="custom_css">Custom CSS</label>
                        <textarea id="custom_css" name="custom_css"><?php echo wiblog_value($design['custom_css']); ?></textarea>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_dark_mode" name="enable_dark_mode" value="1" <?php echo wiblog_checked($design['enable_dark_mode']); ?>>
                        <label for="enable_dark_mode">Enable Dark Mode Support</label>
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>Content & Community</h3>
                <p>Post behaviour, engagement, categories, tags and reader features.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_categories" name="enable_categories" value="1" <?php echo wiblog_checked($content['enable_categories']); ?>>
                        <label for="enable_categories">Enable Categories</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_tags" name="enable_tags" value="1" <?php echo wiblog_checked($content['enable_tags']); ?>>
                        <label for="enable_tags">Enable Tags</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_featured_posts" name="enable_featured_posts" value="1" <?php echo wiblog_checked($content['enable_featured_posts']); ?>>
                        <label for="enable_featured_posts">Enable Featured Posts</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_related_posts" name="enable_related_posts" value="1" <?php echo wiblog_checked($content['enable_related_posts']); ?>>
                        <label for="enable_related_posts">Enable Related Posts</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_author_box" name="enable_author_box" value="1" <?php echo wiblog_checked($content['enable_author_box']); ?>>
                        <label for="enable_author_box">Enable Author Box</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="show_read_time" name="show_read_time" value="1" <?php echo wiblog_checked($content['show_read_time']); ?>>
                        <label for="show_read_time">Show Read Time</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="allow_scheduled_posts" name="allow_scheduled_posts" value="1" <?php echo wiblog_checked($content['allow_scheduled_posts']); ?>>
                        <label for="allow_scheduled_posts">Allow Scheduled Posts</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="allow_private_posts" name="allow_private_posts" value="1" <?php echo wiblog_checked($content['allow_private_posts']); ?>>
                        <label for="allow_private_posts">Allow Private Posts</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_comments" name="enable_comments" value="1" <?php echo wiblog_checked($content['enable_comments']); ?>>
                        <label for="enable_comments">Enable Comments</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="moderate_comments" name="moderate_comments" value="1" <?php echo wiblog_checked($content['moderate_comments']); ?>>
                        <label for="moderate_comments">Moderate Comments</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="allow_guest_comments" name="allow_guest_comments" value="1" <?php echo wiblog_checked($content['allow_guest_comments']); ?>>
                        <label for="allow_guest_comments">Allow Guest Comments</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_share_buttons" name="enable_share_buttons" value="1" <?php echo wiblog_checked($content['enable_share_buttons']); ?>>
                        <label for="enable_share_buttons">Enable Share Buttons</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_likes" name="enable_likes" value="1" <?php echo wiblog_checked($content['enable_likes']); ?>>
                        <label for="enable_likes">Enable Likes</label>
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>SEO</h3>
                <p>Visibility, indexing and social metadata controls.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_seo" name="enable_seo" value="1" <?php echo wiblog_checked($seo['enable_seo']); ?>>
                        <label for="enable_seo">Enable SEO</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_schema" name="enable_schema" value="1" <?php echo wiblog_checked($seo['enable_schema']); ?>>
                        <label for="enable_schema">Enable Schema Markup</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_open_graph" name="enable_open_graph" value="1" <?php echo wiblog_checked($seo['enable_open_graph']); ?>>
                        <label for="enable_open_graph">Enable Open Graph</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_sitemap" name="enable_sitemap" value="1" <?php echo wiblog_checked($seo['enable_sitemap']); ?>>
                        <label for="enable_sitemap">Enable Sitemap</label>
                    </div>

                    <div class="wiblog-field">
                        <label for="default_meta_title">Default Meta Title</label>
                        <input type="text" id="default_meta_title" name="default_meta_title" value="<?php echo wiblog_value($seo['default_meta_title']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="default_meta_description">Default Meta Description</label>
                        <textarea id="default_meta_description" name="default_meta_description"><?php echo wiblog_value($seo['default_meta_description']); ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>Performance</h3>
                <p>Media handling, caching and workflow quality-of-life settings.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-switch">
                        <input type="checkbox" id="require_featured_image" name="require_featured_image" value="1" <?php echo wiblog_checked($performance['require_featured_image']); ?>>
                        <label for="require_featured_image">Require Featured Image</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="lazy_load_images" name="lazy_load_images" value="1" <?php echo wiblog_checked($performance['lazy_load_images']); ?>>
                        <label for="lazy_load_images">Lazy Load Images</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_cache" name="enable_cache" value="1" <?php echo wiblog_checked($performance['enable_cache']); ?>>
                        <label for="enable_cache">Enable Cache</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="allow_post_duplication" name="allow_post_duplication" value="1" <?php echo wiblog_checked($performance['allow_post_duplication']); ?>>
                        <label for="allow_post_duplication">Allow Post Duplication</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_revisions" name="enable_revisions" value="1" <?php echo wiblog_checked($performance['enable_revisions']); ?>>
                        <label for="enable_revisions">Enable Revisions</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_autosave" name="enable_autosave" value="1" <?php echo wiblog_checked($performance['enable_autosave']); ?>>
                        <label for="enable_autosave">Enable Autosave</label>
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>Business & Charity</h3>
                <p>Donation links, sponsor blocks and flexible public-facing calls to action.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_donations" name="enable_donations" value="1" <?php echo wiblog_checked($business['enable_donations']); ?>>
                        <label for="enable_donations">Enable Donations</label>
                    </div>

                    <div class="wiblog-field">
                        <label for="donation_text">Donation Text</label>
                        <input type="text" id="donation_text" name="donation_text" value="<?php echo wiblog_value($business['donation_text']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="donation_url">Donation URL</label>
                        <input type="url" id="donation_url" name="donation_url" value="<?php echo wiblog_value($business['donation_url']); ?>">
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="enable_sponsor_block" name="enable_sponsor_block" value="1" <?php echo wiblog_checked($business['enable_sponsor_block']); ?>>
                        <label for="enable_sponsor_block">Enable Sponsor Block</label>
                    </div>

                    <div class="wiblog-field">
                        <label for="sponsor_text">Sponsor Text</label>
                        <input type="text" id="sponsor_text" name="sponsor_text" value="<?php echo wiblog_value($business['sponsor_text']); ?>">
                    </div>

                    <div class="wiblog-field">
                        <label for="sponsor_url">Sponsor URL</label>
                        <input type="url" id="sponsor_url" name="sponsor_url" value="<?php echo wiblog_value($business['sponsor_url']); ?>">
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-option-card">
            <div class="card-head">
                <h3>Permissions</h3>
                <p>Control publishing rights for editors and authors.</p>
            </div>
            <div class="card-body">
                <div class="wiblog-option-grid">

                    <div class="wiblog-switch">
                        <input type="checkbox" id="editor_can_publish" name="editor_can_publish" value="1" <?php echo wiblog_checked($permissions['editor_can_publish']); ?>>
                        <label for="editor_can_publish">Editors Can Publish</label>
                    </div>

                    <div class="wiblog-switch">
                        <input type="checkbox" id="author_can_publish" name="author_can_publish" value="1" <?php echo wiblog_checked($permissions['author_can_publish']); ?>>
                        <label for="author_can_publish">Authors Can Publish</label>
                    </div>

                </div>
            </div>
        </div>

        <div class="wiblog-actions">
            <button type="submit" class="btn btn-success">Save Options</button>
            <button type="button" class="btn btn-default" onclick="window.location.reload();">Reload</button>
        </div>

    </form>

</div>