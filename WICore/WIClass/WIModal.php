<?php
declare(strict_types=1);

/**
 * Modal Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WIModal
{
    private WIdb $WIdb;
    private WIEditor $Edit;
    private WIImage $Img;
    private WISite $site;
    private WICalendar $Calendar;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Edit = new WIEditor();
        $this->Img  = new WIImage();
        $this->site = new WISite();
        $this->Calendar = new WICalendar();
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function renderBodyContent(string $function, ...$args): void
    {
        if (method_exists($this, $function)) {
            call_user_func_array([$this, $function], $args);
            return;
        }

        echo '<div class="alert alert-danger">Modal content method not found: ' . $this->e($function) . '</div>';
    }

    private function modalStyles(): void
    {
        echo '<style>
            .wi-modal .modal-content{
                border-radius:12px;
                border:0;
                box-shadow:0 12px 35px rgba(0,0,0,.16);
                overflow:hidden;
            }

            .wi-modal .modal-header{
                background:#f8f9fb;
                border-bottom:1px solid #e9ecef;
                padding:16px 20px;
                display:flex;
                align-items:center;
                justify-content:space-between;
            }

            .wi-modal .modal-title{
                font-size:20px;
                font-weight:600;
                margin:0;
                color:#1f2937;
            }

            .wi-modal .modal-body{
                padding:20px;
                background:#ffffff;
            }

            .wi-modal .modal-footer{
                background:#fafbfc;
                border-top:1px solid #e9ecef;
                padding:14px 20px;
                display:flex;
                justify-content:flex-end;
                gap:10px;
                flex-wrap:wrap;
            }

            .wi-modal .close{
                background:none;
                border:0;
                font-size:26px;
                line-height:1;
                color:#6b7280;
                opacity:1;
                cursor:pointer;
                box-shadow:none;
            }

            .wi-modal .close:hover{
                color:#111827;
            }

            .wi-modal .ajax-loading{
                padding:10px 0 0;
            }

            .wi-modal .ajax-loading img{
                max-width:38px;
                height:auto;
            }

            .wi-modal .form-group{
                margin-bottom:15px;
            }

            .wi-modal .form-control{
                border-radius:8px;
                min-height:42px;
            }

            .wi-modal textarea.form-control{
                min-height:120px;
            }

            .wi-modal .btn{
                min-width:110px;
                border-radius:8px;
            }

            .wi-modal .wi-modal-message{
                margin-bottom:15px;
            }
        </style>';
    }

    private function renderModal(
        string $ele_id,
        string $title,
        string $action,
        string $function,
        string $button = '',
        string $footer_b = '',
        array $functionArgs = [],
        bool $installerMode = false
    ): void {
        $safeId = $this->e($ele_id);
        $safeTitle = $this->e($title);

        $this->modalStyles();

        echo '<div class="modal fade wi-modal" id="modal-' . $safeId . '-details" tabindex="-1" role="dialog" aria-labelledby="WIModalLabel-' . $safeId . '" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">';
                        if ($installerMode) {
                            $this->headerInstaller($action, $ele_id, $title);
                        } else {
                            $this->header($action, $ele_id, $title);
                        }
        echo '      </div>

                    <div class="modal-body">
                        <div id="details-body-' . $safeId . '">';
                            $this->renderBodyContent($function, ...$functionArgs);
        echo '          </div>
                    </div>

                    <div align="center" class="ajax-loading hide">
                        <img src="WIMedia/Img/ajax_loader.gif" alt="Loading">
                    </div>

                    <div class="modal-footer">';
                        if (!empty($functionArgs)) {
                            $arg = (string)$functionArgs[0];
                            $this->multifooter($button, $action, $function, $footer_b, $arg);
                        } elseif ($installerMode) {
                            $this->Installerfooter($button, $action, $function, $footer_b);
                        } else {
                            $this->footer($button, $action, $function, $footer_b);
                        }
        echo '      </div>

                </div>
            </div>
        </div>';
    }

    public function new_modal($ele_id, $title, $action, $function, $button): void
    {
        $this->renderModal((string)$ele_id, (string)$title, (string)$action, (string)$function, (string)$button, '');
    }

    public function moduleModal($ele_id, $title, $action, $function, $button, $footer_b): void
    {
        $this->renderModal(
            (string)$ele_id,
            (string)$title,
            (string)$action,
            (string)$function,
            (string)$button,
            (string)$footer_b
        );
    }

    public function multiModuleModal($ele_id, $title, $action, $function, $button, $footer_b, $id): void
    {
        $this->renderModal(
            (string)$ele_id,
            (string)$title,
            (string)$action,
            (string)$function,
            (string)$button,
            (string)$footer_b,
            [(string)$id]
        );
    }

    public function moduleInstallerModal($ele_id, $title, $action, $function, $button, $footer_b): void
    {
        $this->renderModal(
            (string)$ele_id,
            (string)$title,
            (string)$action,
            (string)$function,
            (string)$button,
            (string)$footer_b,
            [],
            true
        );
    }

    public function delete(): void
    {
        echo '<div class="wi-modal-message">
                <p>Are you sure you want to delete this item?</p>
              </div>';
    }

    public function header($action, $ele_id, $title): void
    {
        echo '<h5 class="modal-title" id="WIModalLabel-' . $this->e($ele_id) . '">' . $this->e($title) . '</h5>
            <button type="button" class="close" onclick="' . $this->e($action) . '.closed(`' . $this->e($ele_id) . '`)" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>';
    }

    public function headerInstaller($action, $ele_id, $title): void
    {
        echo '<h5 class="modal-title" id="' . $this->e($ele_id) . '">' . $this->e($title) . '</h5>
            <button type="button" class="close" onclick="' . $this->e($action) . '.closed(`' . $this->e($ele_id) . '`)" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>';
    }

    public function footer($button, $action, $function, $footer_b): void
    {
        $safeButton = trim((string)$button);
        $safeAction = $this->e($action);
        $safeFunction = $this->e($function);
        $safeFooterId = $this->e($footer_b);

        if ($safeButton === 'Next') {
            echo '<button type="button" class="btn btn-light close" data-dismiss="modal">Previous</button>
                  <button id="' . $safeFooterId . '" type="button" class="btn btn-primary" onclick="' . $safeAction . '.' . $safeFunction . '()">' . $this->e($safeButton) . '</button>';
            return;
        }

        if ($safeButton === '') {
            echo '<button type="button" class="btn btn-light close" data-dismiss="modal">Close</button>';
            return;
        }

        echo '<button type="button" class="btn btn-light close" data-dismiss="modal">Close</button>
              <button id="' . $safeFooterId . '" type="button" class="btn btn-primary" onclick="' . $safeAction . '.' . $safeFunction . '()">' . $this->e($safeButton) . '</button>';
    }

    public function multifooter($button, $action, $function, $footer_b, $id): void
    {
        $safeButton = trim((string)$button);
        $safeAction = $this->e($action);
        $safeFunction = $this->e($function);
        $safeFooterId = $this->e($footer_b);
        $safeId = $this->e($id);

        if ($safeButton === 'Next') {
            echo '<button type="button" class="btn btn-light close" data-dismiss="modal">Previous</button>
                  <button id="' . $safeFooterId . '" type="button" class="btn btn-primary" onclick="' . $safeAction . '.' . $safeFunction . '(`' . $safeId . '`)">' . $this->e($safeButton) . '</button>';
            return;
        }

        if ($safeButton === '') {
            echo '<button type="button" class="btn btn-light close" data-dismiss="modal">Close</button>';
            return;
        }

        echo '<button type="button" class="btn btn-light close" data-dismiss="modal">Close</button>
              <button id="' . $safeFooterId . '" type="button" class="btn btn-primary" onclick="' . $safeAction . '.' . $safeFunction . '(`' . $safeId . '`)">' . $this->e($safeButton) . '</button>';
    }

    public function Installerfooter($button, $action, $function, $footer_b): void
    {
        $this->footer($button, $action, $function, $footer_b);
    }

    public function SaveContent()
    {
        return $this->Edit->WIEdit();
    }

    public function ExpressInterest(): void
    {
        echo '<form class="form-express-interest" id="express-interest-form">
            <div id="maclass" class="maclass wi-modal-message"></div>

            <div class="form-group row">
                <label class="control-label col-lg-3 col-md-3 col-sm-4 col-12" for="fullname">' . $this->e(WILang::get("full_name")) . '</label>
                <div class="controls col-lg-9 col-md-9 col-sm-8 col-12">
                    <input id="fullname" name="fullname" type="text" class="input-medium form-control" autocomplete="name">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-lg-3 col-md-3 col-sm-4 col-12" for="mobile">' . $this->e(WILang::get("phone_number")) . '</label>
                <div class="controls col-lg-9 col-md-9 col-sm-8 col-12">
                    <input id="mobile" name="mobile" type="text" class="input-medium form-control" autocomplete="tel">
                </div>
            </div>

            <div class="form-group row">
                <label class="control-label col-lg-3 col-md-3 col-sm-4 col-12" for="email">' . $this->e(WILang::get("email")) . '</label>
                <div class="controls col-lg-9 col-md-9 col-sm-8 col-12">
                    <input id="email" name="email" type="email" class="input-medium form-control" autocomplete="email">
                </div>
            </div>

            <div id="maMessage" class="wi-modal-message"></div>
        </form>';
    }

    public function cookieConsentPreferences(): void
    {
        if (!class_exists('WIConsentManager')) {
            $managerClass = __DIR__ . '/WIConsentManager.php';
            if (is_file($managerClass)) {
                require_once $managerClass;
            }
        }

        if (!class_exists('WIConsentManager')) {
            echo '<div class="wi-modal-message">Cookie preference manager could not be loaded.</div>';
            return;
        }

        $manager = new WIConsentManager();
        $categories = $manager->getCategories();

        echo '<div class="wi-cookie-modal-body" data-wi-cookie-modal-body>';
        echo '<p class="wi-cookie-modal-intro">Choose which optional cookies and similar technologies this site can use. Strictly necessary cookies are required for login, security, sessions and form protection.</p>';

        foreach ($categories as $category) {
            $key = (string) ($category['category_key'] ?? '');
            if ($key === '') {
                continue;
            }

            $label = (string) ($category['label'] ?? $key);
            $description = (string) ($category['description'] ?? '');
            $required = (int) ($category['is_required'] ?? 0) === 1;
            $enabled = (int) ($category['is_enabled'] ?? 0) === 1 || $required;

            echo '<label class="wi-cookie-choice">';
            echo '<span><strong>' . $this->e($label) . '</strong><small>' . $this->e($description) . '</small></span>';
            echo '<input type="checkbox" data-wi-cookie-category="' . $this->e($key) . '" value="1"' . ($enabled ? ' checked' : '') . ($required ? ' disabled' : '') . '>';
            echo '<i aria-hidden="true"></i>';
            echo '</label>';
        }

        echo '<div class="wi-cookie-modal-actions">';
        echo '<button type="button" class="wi-cookie-btn wi-cookie-btn--ghost" data-wi-cookie-reject>Reject non-essential</button>';
        echo '<button type="button" class="wi-cookie-btn wi-cookie-btn--primary" data-wi-cookie-save>Save preferences</button>';
        echo '</div>';
        echo '</div>';
    }

}
?>