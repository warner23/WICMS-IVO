<?php

declare(strict_types=1);

require_once __DIR__ . '/WICompliance.php';

class WIBlog extends WICompliance
{
    public function Cat(): void
    {
        echo '<div class="wic-panel"><h3>Compliance Areas</h3><ul class="wic-list">';
        foreach ($this->getMenu() as $key => $label) {
            echo '<li data-area="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        echo '</ul></div>';
    }

    public function hasPosts(): void
    {
        echo $this->renderAdminDashboard();
    }

    public function noMedia($day, $month, $post_title, $blog_post, $type, $href, $user, $button_name): void
    {
        $this->addChecklist([
            'title' => $post_title,
            'category' => $type ?: 'general',
            'frequency' => 'daily',
            'assigned_role' => $user ?: 'manager',
            'evidence_required' => 0,
        ]);
    }

    public function blogPostImage($day, $month, $post_title, $blog_post, $type, $href, $user, $button_name, $image): void
    {
        $this->addChecklist([
            'title' => $post_title,
            'category' => $type ?: 'general',
            'frequency' => 'daily',
            'assigned_role' => $user ?: 'manager',
            'evidence_required' => 1,
        ]);
    }

    public function blogPostVideo($day, $month, $post_title, $blog_post, $type, $href, $user, $button_name, $video): void
    {
        $this->addChecklist([
            'title' => $post_title,
            'category' => $type ?: 'general',
            'frequency' => 'weekly',
            'assigned_role' => $user ?: 'manager',
            'evidence_required' => 1,
        ]);
    }

    public function Resource(): void
    {
        echo '<div class="wic-panel"><h3>Help & Guidance</h3><p>Use the setup wizard, legal register and checklist engine to generate an audit-ready hospitality compliance workflow.</p></div>';
    }

    public function selectCat($cid): void
    {
        $this->Resource();
    }

    public function Search($keywords): void
    {
        echo '<div class="wic-panel"><h3>Search</h3><p>Searched for: ' . htmlspecialchars((string) $keywords, ENT_QUOTES, 'UTF-8') . '</p></div>';
    }
}
