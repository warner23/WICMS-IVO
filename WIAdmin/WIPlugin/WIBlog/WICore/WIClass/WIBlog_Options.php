<?php
declare(strict_types=1);

/**
 * WIBlog Options Logic
 * Location: WIPlugin/WIBlog/WICore/WIClass/WIBlog_Options.php
 */

#[\AllowDynamicProperties]
class WIBlog_Options
{
    private WIdb $WIdb;
    private array $manifest;

    public function __construct(array $manifest = [])
    {
        $this->WIdb = WIdb::getInstance();
        $this->manifest = $manifest;
    }

    /**
     * Default settings for the plugin.
     */
    public function defaults(): array
    {
        return [

            // General
            'blog_name' => 'My Blog',
            'blog_subtitle' => '',
            'default_language' => 'en',
            'timezone' => 'UTC',
            'date_format' => 'F j, Y',
            'posts_per_page' => '10',
            'excerpt_length' => '140',

            // Layout / Design
            'layout_style' => 'grid',
            'card_style' => 'modern',
            'accent_color' => '#3498db',
            'secondary_color' => '#2c3e50',
            'background_color' => '#ffffff',
            'text_color' => '#333333',
            'border_radius' => '8',
            'enable_dark_mode' => '0',
            'custom_css' => '',

            // Content / Features
            'enable_categories' => '1',
            'enable_tags' => '1',
            'enable_featured_posts' => '1',
            'enable_related_posts' => '1',
            'enable_author_box' => '1',
            'show_read_time' => '1',
            'allow_scheduled_posts' => '1',
            'allow_private_posts' => '1',

            // Comments / Community
            'enable_comments' => '1',
            'moderate_comments' => '1',
            'allow_guest_comments' => '0',
            'enable_share_buttons' => '1',
            'enable_likes' => '0',

            // SEO
            'enable_seo' => '1',
            'enable_schema' => '1',
            'enable_open_graph' => '1',
            'enable_sitemap' => '1',
            'default_meta_title' => '',
            'default_meta_description' => '',

            // Media / Performance
            'require_featured_image' => '0',
            'lazy_load_images' => '1',
            'enable_cache' => '0',

            // Business / Charity
            'enable_donations' => '0',
            'donation_text' => '',
            'donation_url' => '',
            'enable_sponsor_block' => '0',
            'sponsor_text' => '',
            'sponsor_url' => '',

            // Permissions / Workflow
            'allow_post_duplication' => '1',
            'enable_revisions' => '1',
            'enable_autosave' => '1',
            'editor_can_publish' => '0',
            'author_can_publish' => '0',
        ];
    }

    /**
     * Sanitize incoming value by key.
     */
    public function sanitize(string $key, $value): string
    {
        $checkboxKeys = [
            'enable_dark_mode',
            'enable_categories',
            'enable_tags',
            'enable_featured_posts',
            'enable_related_posts',
            'enable_author_box',
            'show_read_time',
            'allow_scheduled_posts',
            'allow_private_posts',
            'enable_comments',
            'moderate_comments',
            'allow_guest_comments',
            'enable_share_buttons',
            'enable_likes',
            'enable_seo',
            'enable_schema',
            'enable_open_graph',
            'enable_sitemap',
            'require_featured_image',
            'lazy_load_images',
            'enable_cache',
            'enable_donations',
            'enable_sponsor_block',
            'allow_post_duplication',
            'enable_revisions',
            'enable_autosave',
            'editor_can_publish',
            'author_can_publish',
        ];

        $intKeys = [
            'posts_per_page',
            'excerpt_length',
            'border_radius',
        ];

        $colorKeys = [
            'accent_color',
            'secondary_color',
            'background_color',
            'text_color',
        ];

        if (in_array($key, $checkboxKeys, true)) {
            return ((string)$value === '1' || $value === 1 || $value === true || $value === 'on') ? '1' : '0';
        }

        if (in_array($key, $intKeys, true)) {
            return (string)max(0, (int)$value);
        }

        if (in_array($key, $colorKeys, true)) {
            $value = trim((string)$value);

            if (preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
                return $value;
            }

            return (string)($this->defaults()[$key] ?? '#000000');
        }

        return trim((string)$value);
    }

    /**
     * Get one setting.
     */
    public function get(string $key, ?string $default = null): ?string
    {
        $rows = $this->WIdb->select(
            "SELECT `setting_value`
             FROM `wi_blog_settings`
             WHERE `setting_key` = :key
             LIMIT 1",
            ['key' => $key]
        );

        if (!empty($rows[0]['setting_value'])) {
            return (string)$rows[0]['setting_value'];
        }

        if ($default !== null) {
            return $default;
        }

        $defaults = $this->defaults();
        return isset($defaults[$key]) ? (string)$defaults[$key] : null;
    }

    /**
     * Return all stored settings merged with defaults.
     */
    public function all(): array
    {
        $defaults = $this->defaults();
        $rows = $this->WIdb->select("SELECT * FROM `wi_blog_settings` ORDER BY `setting_key` ASC");

        foreach ($rows as $row) {
            $key = (string)($row['setting_key'] ?? '');
            $value = (string)($row['setting_value'] ?? '');

            if ($key !== '') {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    /**
     * Save one setting.
     */
    public function set(string $key, $value): bool
    {
        $value = $this->sanitize($key, $value);

        $exists = $this->WIdb->select(
            "SELECT `id`
             FROM `wi_blog_settings`
             WHERE `setting_key` = :key
             LIMIT 1",
            ['key' => $key]
        );

        if (!empty($exists)) {
            return (bool)$this->WIdb->update(
                'wi_blog_settings',
                ['setting_value' => $value],
                '`setting_key` = :key',
                ['key' => $key]
            );
        }

        return (bool)$this->WIdb->insert(
            'wi_blog_settings',
            [
                'setting_key' => $key,
                'setting_value' => $value
            ]
        );
    }

    /**
     * Save many settings.
     */
    public function saveMany(array $settings): bool
    {
        $ok = true;

        foreach ($settings as $key => $value) {
            if (!$this->set((string)$key, $value)) {
                $ok = false;
            }
        }

        return $ok;
    }

    /**
     * Delete one setting.
     */
    public function delete(string $key): bool
    {
        return (bool)$this->WIdb->delete(
            'wi_blog_settings',
            '`setting_key` = :key',
            ['key' => $key]
        );
    }

    /**
     * Reset settings back to defaults.
     */
    public function resetToDefaults(): bool
    {
        $rows = $this->WIdb->select("SELECT `id` FROM `wi_blog_settings`");
        $ok = true;

        foreach ($rows as $row) {
            $id = (int)($row['id'] ?? 0);

            if ($id > 0) {
                $deleted = $this->WIdb->delete(
                    'wi_blog_settings',
                    '`id` = :id',
                    ['id' => $id]
                );

                if (!$deleted) {
                    $ok = false;
                }
            }
        }

        return $this->initialiseDefaults() && $ok;
    }

    /**
     * Insert defaults if missing.
     */
    public function initialiseDefaults(): bool
    {
        $defaults = $this->defaults();
        $ok = true;

        foreach ($defaults as $key => $value) {
            $exists = $this->WIdb->select(
                "SELECT `id`
                 FROM `wi_blog_settings`
                 WHERE `setting_key` = :key
                 LIMIT 1",
                ['key' => $key]
            );

            if (empty($exists)) {
                $inserted = $this->WIdb->insert(
                    'wi_blog_settings',
                    [
                        'setting_key' => (string)$key,
                        'setting_value' => (string)$value
                    ]
                );

                if (!$inserted) {
                    $ok = false;
                }
            }
        }

        return $ok;
    }

    /**
     * General option group
     */
    public function generalOptions(): array
    {
        $all = $this->all();

        return [
            'blog_name' => $all['blog_name'],
            'blog_subtitle' => $all['blog_subtitle'],
            'default_language' => $all['default_language'],
            'timezone' => $all['timezone'],
            'date_format' => $all['date_format'],
            'posts_per_page' => $all['posts_per_page'],
            'excerpt_length' => $all['excerpt_length'],
        ];
    }

    /**
     * Design option group
     */
    public function designOptions(): array
    {
        $all = $this->all();

        return [
            'layout_style' => $all['layout_style'],
            'card_style' => $all['card_style'],
            'accent_color' => $all['accent_color'],
            'secondary_color' => $all['secondary_color'],
            'background_color' => $all['background_color'],
            'text_color' => $all['text_color'],
            'border_radius' => $all['border_radius'],
            'enable_dark_mode' => $all['enable_dark_mode'],
            'custom_css' => $all['custom_css'],
        ];
    }

    /**
     * Content/community option group
     */
    public function contentOptions(): array
    {
        $all = $this->all();

        return [
            'enable_categories' => $all['enable_categories'],
            'enable_tags' => $all['enable_tags'],
            'enable_featured_posts' => $all['enable_featured_posts'],
            'enable_related_posts' => $all['enable_related_posts'],
            'enable_author_box' => $all['enable_author_box'],
            'show_read_time' => $all['show_read_time'],
            'allow_scheduled_posts' => $all['allow_scheduled_posts'],
            'allow_private_posts' => $all['allow_private_posts'],
            'enable_comments' => $all['enable_comments'],
            'moderate_comments' => $all['moderate_comments'],
            'allow_guest_comments' => $all['allow_guest_comments'],
            'enable_share_buttons' => $all['enable_share_buttons'],
            'enable_likes' => $all['enable_likes'],
        ];
    }

    /**
     * SEO option group
     */
    public function seoOptions(): array
    {
        $all = $this->all();

        return [
            'enable_seo' => $all['enable_seo'],
            'enable_schema' => $all['enable_schema'],
            'enable_open_graph' => $all['enable_open_graph'],
            'enable_sitemap' => $all['enable_sitemap'],
            'default_meta_title' => $all['default_meta_title'],
            'default_meta_description' => $all['default_meta_description'],
        ];
    }

    /**
     * Performance option group
     */
    public function performanceOptions(): array
    {
        $all = $this->all();

        return [
            'require_featured_image' => $all['require_featured_image'],
            'lazy_load_images' => $all['lazy_load_images'],
            'enable_cache' => $all['enable_cache'],
            'allow_post_duplication' => $all['allow_post_duplication'],
            'enable_revisions' => $all['enable_revisions'],
            'enable_autosave' => $all['enable_autosave'],
        ];
    }

    /**
     * Business / charity option group
     */
    public function businessOptions(): array
    {
        $all = $this->all();

        return [
            'enable_donations' => $all['enable_donations'],
            'donation_text' => $all['donation_text'],
            'donation_url' => $all['donation_url'],
            'enable_sponsor_block' => $all['enable_sponsor_block'],
            'sponsor_text' => $all['sponsor_text'],
            'sponsor_url' => $all['sponsor_url'],
        ];
    }

    /**
     * Permissions option group
     */
    public function permissionOptions(): array
    {
        $all = $this->all();

        return [
            'editor_can_publish' => $all['editor_can_publish'],
            'author_can_publish' => $all['author_can_publish'],
        ];
    }
}
?>