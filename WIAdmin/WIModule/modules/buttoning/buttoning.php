<?php
declare(strict_types=1);

/** Clean WICMS placeholder for legacy buttoning module. */
class buttoning
{
    public function __construct() {}
    public function editMod(): void { echo '<div class="wi-admin-preview"><h2>Button module</h2><p>Clean placeholder.</p></div>'; }
    public function editPageContent($page_id = null): void { $this->editMod(); }
    public function mod_name(): void { echo '<div class="wi-buttoning-module">Button module</div>'; }
}
