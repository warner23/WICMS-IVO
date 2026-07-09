<?php
/**
 * WIMedia Centre View
 *
 * Location: /WIAdmin/WIInc/media/media_centre.php
 * Layer: UI only
 */

require_once dirname(__DIR__, 2) . '/WICore/WIClass/Media/WIMediaCentreRenderer.php';

$context = is_array($mediaContext ?? null) ? $mediaContext : [];

echo (new WIMediaCentreRenderer())->render($context);
