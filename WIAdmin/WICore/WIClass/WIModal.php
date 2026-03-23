<?php
#[\AllowDynamicProperties]
class WIModal
{
    protected WIdb $WIdb;
    protected WIEditor $Edit;
    protected WIImage $Img;
    protected WISite $site;
    protected WIPage $page;
    protected $forum = null;
    protected $pos = null;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Edit = new WIEditor();
        $this->Img  = new WIImage();
        $this->site = new WISite();
        $this->page = new WIPage();

        if (class_exists('WIForum')) {
            $this->forum = new WIForum();
        }

        if (class_exists('WIPos')) {
            $this->pos = new WIPos();
        }
    }

    public function moduleModal($ele_id, $title, $action, $function, $button, $footer_b = '')
    {
        $modalId = 'modal-' . $this->esc($ele_id) . '-details';

        echo '<div class="modal hide wi-modal" id="' . $modalId . '" tabindex="-1" role="dialog" aria-labelledby="WIModalLabel-' . $this->esc($ele_id) . '" aria-hidden="true">';
        echo '  <div class="modal-dialog" role="document">';
        echo '    <div class="modal-content wi-modal-content">';
        echo '      <div class="modal-header wi-modal-header">';
        $this->header($action, $ele_id, $title);
        echo '      </div>';
        echo '      <div class="modal-body wi-modal-body">';
        echo '        <div id="details-body">';
        if (method_exists($this, $function)) {
            $this->{$function}();
        } else {
            echo '<div class="alert alert-warning">Modal view method <strong>' . $this->esc($function) . '</strong> was not found.</div>';
        }
        echo '        </div>';
        echo '      </div>';
        echo '      <div align="center" class="ajax-loading hide wi-modal-loader">';
        echo '        <img src="WIMedia/Img/ajax_loader.gif" alt="Loading">';
        echo '      </div>';
        echo '      <div class="modal-footer wi-modal-footer">';
        $this->footer($ele_id, $button, $action, $function, $footer_b);
        echo '      </div>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }

    public function header($action, $ele_id, $title)
    {
        echo '<h5 class="modal-title" id="WIModalLabel-' . $this->esc($ele_id) . '">' . $this->esc($title) . '</h5>';
        echo '<button type="button" class="close" onclick="' . $this->closeAction($action, $ele_id) . '" data-dismiss="modal" aria-label="Close">';
        echo '  <span aria-hidden="true">&times;</span>';
        echo '</button>';
    }

    public function footer($ele_id, $button, $action, $function, $footer_b)
    {
        if ($button === '') {
            echo '<button type="button" class="btn btn-secondary modal_close" data-dismiss="modal" onclick="' . $this->closeAction($action, $ele_id) . '">Close</button>';
            return;
        }

        $footerId = $footer_b !== '' ? ' id="' . $this->esc($footer_b) . '"' : '';

        echo '<button type="button" class="btn btn-secondary modal_close" data-dismiss="modal" onclick="' . $this->closeAction($action, $ele_id) . '">Close</button>';
        echo '<button' . $footerId . ' type="button" class="btn btn-primary" onclick="' . $this->buttonAction($action, $function) . '">' . $this->esc(rtrim($button, '.')) . '</button>';
    }

    protected function esc($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    protected function lang(string $key, ?string $fallback = null): string
    {
        try {
            $value = WILang::get($key);
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        } catch (Throwable $e) {
        }

        return $fallback ?? $key;
    }

    protected function closeAction(string $action, string $eleId): string
    {
        return $action . '.closed(`' . $this->esc($eleId) . '`)';
    }

    protected function buttonAction(string $action, string $function): string
    {
        return $action . '.' . $function . '()';
    }

    protected function openForm(string $id): void
    {
        echo '<form class="form-horizontal wi-modal-form" id="' . $this->esc($id) . '">';
    }

    protected function closeForm(): void
    {
        echo '</form>';
    }

    protected function hiddenField(string $id, string $name, string $value = ''): void
    {
        echo '<input id="' . $this->esc($id) . '" name="' . $this->esc($name) . '" type="hidden" class="form-control" value="' . $this->esc($value) . '">';
    }

    protected function inputRow(string $id, string $name, string $label, string $type = 'text', string $value = '', string $placeholder = ''): void
    {
        echo '<div class="form-group">';
        echo '  <label class="control-label col-lg-3" for="' . $this->esc($id) . '">' . $this->esc($label) . '</label>';
        echo '  <div class="col-lg-9">';
        echo '    <input id="' . $this->esc($id) . '" name="' . $this->esc($name) . '" type="' . $this->esc($type) . '" class="input-xlarge form-control" value="' . $this->esc($value) . '"' . ($placeholder !== '' ? ' placeholder="' . $this->esc($placeholder) . '"' : '') . '>';
        echo '  </div>';
        echo '</div>';
    }

    protected function textareaRow(string $id, string $name, string $label, string $value = '', int $rows = 4): void
    {
        echo '<div class="form-group">';
        echo '  <label class="control-label col-lg-3" for="' . $this->esc($id) . '">' . $this->esc($label) . '</label>';
        echo '  <div class="col-lg-9">';
        echo '    <textarea id="' . $this->esc($id) . '" name="' . $this->esc($name) . '" class="input-xlarge form-control" rows="' . (int) $rows . '">' . $this->esc($value) . '</textarea>';
        echo '  </div>';
        echo '</div>';
    }

    protected function selectPageRow(string $selectId, string $hiddenId, string $hiddenName = ''): void
    {
        echo '<div class="form-group">';
        echo '  <div class="col-lg-9 col-lg-offset-3">';
        echo '    <select id="' . $this->esc($selectId) . '" class="form-control">';
        $this->page->selectPage();
        echo '    </select>';
        echo '    <input id="' . $this->esc($hiddenId) . '" name="' . $this->esc($hiddenName !== '' ? $hiddenName : $hiddenId) . '" type="hidden" class="form-control">';
        echo '  </div>';
        echo '</div>';
    }

    protected function confirmDeleteBox(string $class = 'delete_id', string $message = 'Are you sure you want to delete this item?'): void
    {
        echo '<div class="' . $this->esc($class) . ' wi-delete-confirm" id=""><p>' . $this->esc($message) . '</p></div>';
    }

    protected function mediaChoice(string $libraryAction, string $uploadAction): void
    {
        echo '<div class="wi-media-choice">';
        echo '  <button type="button" class="btn btn-primary" onclick="' . $this->esc($libraryAction) . '">Upload from WIMedia Library</button>';
        echo '  <button type="button" class="btn btn-default" onclick="' . $this->esc($uploadAction) . '">Upload from computer</button>';
        echo '</div>';
    }

    protected function wrapImagePicker(callable $callback, bool $showHeading = true): void
    {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 wi-media-browser">';
        if ($showHeading) {
            echo '<h3 class="wi-modal-section-title">' . $this->esc($this->lang('Media_Lib', 'Media Library')) . '</h3>';
        }
        $callback();
        echo '</div>';
    }

    protected function wrapUpload(callable $callback): void
    {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 wi-media-upload">';
        $callback();
        echo '</div>';
    }

    protected function optionList(string $table, string $valueField = 'id', string $labelField = 'name'): void
    {
        $rows = $this->WIdb->bindfree('SELECT * FROM `' . $table . '`');
        foreach ($rows as $row) {
            echo '<option value="' . $this->esc($row[$valueField] ?? '') . '">' . $this->esc($row[$labelField] ?? '') . '</option>';
        }
    }

    // Generic delete wrappers kept for current call sites
    public function delete() { $this->confirmDeleteBox(); }
    public function transitemdelete() { $this->confirmDeleteBox(); }
    public function deleteMEnu() { $this->confirmDeleteBox(); }
    public function deletecategories() { $this->confirmDeleteBox(); }
    public function deletebrand() { $this->confirmDeleteBox(); }
    public function Deletetheme() { $this->confirmDeleteBox(); }
    public function deleteCss() { $this->confirmDeleteBox(); }
    public function Deletejs() { $this->confirmDeleteBox(); }
    public function DeleteMeta() { $this->confirmDeleteBox(); }
    public function DeleteCategory() { $this->confirmDeleteBox(); }
    public function DeleteSection() { $this->confirmDeleteBox(); }
    public function deleteAdminMenu() { $this->confirmDeleteBox('delete_admin_menu_id', 'Are you sure you want to delete this admin menu item?'); }

    // Language
    public function editing()
    {
        $this->openForm('add_trans');
        $this->inputRow('lang_name', 'lang_name', $this->lang('trans_lang', 'Language'));
        $this->inputRow('keyword', 'keyword', $this->lang('lang_keyword', 'Keyword'));
        $this->inputRow('translation', 'translation', $this->lang('lang_trans', 'Translation'));
        $this->closeForm();
    }

    public function addingLang()
    {
        $this->openForm('add_trans');
        $this->inputRow('lang_namep', 'lang_name', $this->lang('trans_lang', 'Language'));
        $this->inputRow('keywordp', 'keyword', $this->lang('lang_keyword', 'Keyword'));
        $this->textareaRow('translationp', 'translation', $this->lang('lang_trans', 'Translation'));
        $this->closeForm();
    }

    public function AddLang()
    {
        $this->openForm('add_lang');
        $this->inputRow('lang', 'lang', $this->lang('add_lang', 'Language'), 'text', '', 'English');
        $this->inputRow('code', 'code', 'Code', 'text', '', 'en');
        echo '<div class="form-group">';
        echo '  <div class="col-lg-9 col-lg-offset-3" id="addimg"><img id="AddFlag" src="" alt=""></div>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '  <div class="col-lg-9 col-lg-offset-3"><button type="button" class="btn btn-default" onclick="WILang.AddFlag();">Add Flag</button></div>';
        echo '</div>';
        $this->closeForm();
    }

    public function SaveEditLang()
    {
        $this->openForm('edit_lang');
        $this->hiddenField('editid', 'editid');
        $this->inputRow('editlangname', 'editlang', $this->lang('edit_lang', 'Edit Language'));
        $this->inputRow('editlangcode', 'editlangcode', 'Code');
        echo '<div class="form-group">';
        echo '  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-lg-offset-3" id="editimglang">';
        echo '    <img class="img-responsive" src="WIMedia/Img/lang/" id="imglang" alt="" title="">';
        echo '    <a href="javascript:void(0);" id="changepicbutton">Change Picture</a>';
        echo '  </div>';
        echo '</div>';
        $this->closeForm();
    }

    public function EditTrans()
    {
        $this->openForm('add-user-form');
        $this->hiddenField('trans_id', 'trans_id');
        $this->inputRow('language', 'language', $this->lang('trans_lang', 'Language'));
        $this->inputRow('edit_keyword', 'keyword', $this->lang('lang_keyword', 'Keyword'));
        $this->inputRow('edit_trans', 'trans', $this->lang('lang_trans', 'Translation'));
        $this->closeForm();
    }

    public function Addtrans()
    {
        $this->openForm('add_trans');
        $this->inputRow('lang_name', 'lang_name', $this->lang('trans_lang', 'Language'));
        $this->inputRow('keyword', 'keyword', $this->lang('lang_keyword', 'Keyword'));
        $this->inputRow('translation', 'translation', $this->lang('lang_trans', 'Translation'));
        $this->closeForm();
    }

    // User management
    public function userDetails()
    {
        echo '<dl class="dl-horizontal">';
        foreach ([
            'email' => 'modal-email',
            'first_name' => 'modal-first-name',
            'last_name' => 'modal-last-name',
            'address' => 'modal-address',
            'phone' => 'modal-phone',
            'last_login' => 'modal-last-login',
        ] as $key => $id) {
            echo '<dt title="' . $this->esc($this->lang($key, ucwords(str_replace('_', ' ', $key)))) . '">' . $this->esc($this->lang($key, ucwords(str_replace('_', ' ', $key)))) . '</dt>';
            echo '<dd id="' . $this->esc($id) . '"></dd>';
        }
        echo '</dl>';
    }

    public function changeRoles()
    {
        $roles = $this->WIdb->bindfree("SELECT * FROM `wi_user_roles` WHERE `role_id` <> '3'");
        if (count($roles) < 1) {
            echo '<div class="alert alert-info">There are no roles to display currently.</div>';
            return;
        }

        echo '<p>' . $this->esc($this->lang('select_role', 'Select role')) . '</p>';
        echo '<select id="select-user-role" class="form-control" style="width: 100%;">';
        foreach ($roles as $role) {
            echo '<option value="' . $this->esc($role['role_id']) . '">' . $this->esc(ucfirst((string) $role['role'])) . '</option>';
        }
        echo '</select>';
    }

    public function editUser()
    {
        $this->openForm('add-user-form');
        $this->hiddenField('adduser-userId', 'adduser-userId');
        $this->inputRow('adduser-email', 'adduser-email', $this->lang('email', 'Email'));
        $this->inputRow('adduser-username', 'adduser-username', $this->lang('username', 'Username'));
        $this->inputRow('adduser-password', 'adduser-password', $this->lang('password', 'Password'), 'password');
        $this->inputRow('adduser-confirm_password', 'adduser-confirm_password', $this->lang('repeat_password', 'Repeat password'), 'password');
        echo '<hr>';
        $this->closeForm();
    }

    // Theme / styling
    public function theme()
    {
        $this->openForm('new_theme');
        $this->inputRow('lang_namep', 'lang_name', 'Name');
        $this->closeForm();
    }

    public function addtheme()
    {
        $this->openForm('addnewtheme');
        $this->inputRow('addtheme', 'addtheme', $this->lang('add_theme', 'Add theme'), 'text', '', 'theme');
        $this->closeForm();
    }

    public function enabler()
    {
        echo '<form class="form-horizontal wi-modal-form" id="ele_enable">';
        echo '  <div class="form-group">';
        echo '    <label class="control-label col-lg-3" for="element_enabler">Do you want to enable all your components?</label>';
        echo '    <div class="col-lg-9">';
        echo '      <button type="button" class="btn btn-default">No</button> ';
        echo '      <button type="button" class="btn btn-primary">Yes</button>';
        echo '    </div>';
        echo '  </div>';
        echo '</form>';
    }

    public function saveHtml()
    {
        echo '<form class="form-horizontal wi-modal-form" id="savehtml">';
        echo '  <div class="form-group">';
        echo '    <div class="col-lg-12">';
        echo '      <p>Choose how to save your layout</p>';
        echo '      <div class="btn-group">';
        echo '        <button type="button" id="fluidPage" class="active btn btn-info">Fluid Page</button>';
        echo '        <button type="button" class="btn btn-info" id="fixedPage">Fixed Page</button>';
        echo '      </div>';
        echo '      <br><br><textarea class="form-control" rows="6"></textarea>';
        echo '    </div>';
        echo '  </div>';
        echo '</form>';
    }

    public function SaveContent()
    {
        return $this->Edit->WIEdit();
    }

    // Media choice modals
    public function changepic() { $this->mediaChoice('WIMedia.media()', 'WIMedia.upload()'); }
    public function changeProductPic() { $this->mediaChoice('WIMedia.ProductMedia()', 'WIMedia.ProductUpload()'); }
    public function pagemedia() { $this->mediaChoice('WIMedia.PagePics()', 'WIMedia.PageUploadPics()'); }
    public function langchangepic() { $this->mediaChoice('WILang.langmedia()', 'WILang.editLangupload()'); }
    public function addlangchangepic() { $this->mediaChoice('WILang.addlangmedia()', 'WILang.addLangupload()'); }
    public function createMove() { $this->mediaChoice('WIMMA.addmamedia()', 'WIMMA.addmaupload()'); }
    public function team_edit() { $this->mediaChoice('WITeam.addteammedia()', 'WITeam.addteamupload()'); }
    public function changefavpic() { $this->mediaChoice('WIMedia.changeiconPic()', 'WIMedia.faviconupload()'); }
    public function changeFloorPlanPic() { $this->mediaChoice('WIMedia.changeFloorPlanPic()', 'WIMedia.floorplanupload()'); }

    public function changeMenuPic()
    {
        echo '<div class="wi-media-choice">';
        echo '  <a href="javascript:void(0);" class="btn btn-primary" onclick="WIMedia.foodPics()">Upload from WIMedia Library</a>';
        echo '  <a href="javascript:void(0);" class="btn btn-default" onclick="WIMedia.foodPicsupload()">Upload from computer</a>';
        echo '</div>';
    }

    // Media browsers
    public function HeaderPics() { $this->wrapImagePicker(fn() => $this->Img->HeaderPics()); }
    public function PagePics() { $this->wrapImagePicker(fn() => $this->Img->PagePics()); }
    public function PageMediaPics() { $this->wrapImagePicker(fn() => $this->Img->PagePics()); }
    public function ProductPics() { $this->wrapImagePicker(fn() => $this->Img->ProductPics()); }
    public function TeamPic() { $this->wrapImagePicker(fn() => $this->Img->TeamPic()); }
    public function LangPics() { $this->wrapImagePicker(fn() => $this->Img->LangPics(), false); }
    public function addLangPics() { $this->wrapImagePicker(fn() => $this->Img->addLangPics()); }
    public function faviconPics() { $this->wrapImagePicker(fn() => $this->Img->faviconPics()); }
    public function floorplanPics() { $this->wrapImagePicker(fn() => $this->Img->floorPlanPics()); }
    public function foodPics() { $this->wrapImagePicker(fn() => $this->Img->foodPics()); }

    // Upload panes
    public function UploadPics() { $this->wrapUpload(fn() => $this->site->headerDisplay()); }
    public function addUploadPics() { $this->wrapUpload(fn() => $this->site->AddLangDisplay()); }
    public function editUploadPics() { $this->wrapUpload(fn() => $this->site->EditLangDisplay()); }
    public function PageUploadPics() { $this->wrapUpload(fn() => $this->site->pageDisplay()); }

    public function PageMediaUploadPics()
    {
        $this->wrapUpload(fn() => $this->site->pageModuleDisplay());
        echo '<div align="center" class="ajax-loading hide"><img src="WIMedia/Img/ajax_loader.gif" alt="Loading"></div>';
    }

    public function UploadProductPics() { $this->wrapUpload(fn() => $this->site->ProductDisplay()); }
    public function UploadTeamPics() { $this->wrapUpload(fn() => $this->site->UploadTeamPics()); }
    public function UploadfavPics() { $this->wrapUpload(fn() => $this->site->faviconDisplay()); }
    public function UploadFloorPlanPics() { $this->wrapUpload(fn() => $this->pos ? $this->pos->floorPlanDisplay() : null); }
    public function foodPicsupload() { $this->wrapUpload(fn() => $this->pos ? $this->pos->foddPicsDisplay() : null); }

    // Modules
    public function assign()
    {
        $result = $this->WIdb->select('SELECT * FROM `wi_modules`');
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 wi-module-assign">';
        foreach ($result as $res) {
            $name = (string) ($res['name'] ?? '');
            echo '<button type="button" class="btn btn-default" id="' . $this->esc($name) . '" onclick="WIEditpage.assign(`' . $this->esc($name) . '`)">' . $this->esc($name) . '</button> ';
        }
        echo '</div><div align="center" class="ajax-loading"><img src="ajax_loader.gif" alt="Loading"></div>';
    }

    // Menus
    public function menuEdit()
    {
        $this->openForm('menuedit');
        $this->hiddenField('edit_menu_id', 'edit_menu_id');
        $this->inputRow('edit_menu_name', 'edit_menu_name', 'Name');
        $this->inputRow('edit_menu_link', 'edit_menu_link', 'Link');
        $this->closeForm();
    }

    public function menunew()
    {
        $this->openForm('menunew');
        $this->inputRow('new_menu_name', 'new_menu_name', 'Name');
        $this->inputRow('new_menu_link', 'new_menu_link', 'Link');
        $this->closeForm();
    }

    public function menuLink()
    {
        $this->openForm('menunew');
        $this->inputRow('new_menu_name', 'new_menu_name', 'Name');
        $this->inputRow('new_menu_link', 'new_menu_link', 'Link');
        $this->closeForm();
    }

    public function adminMenuEdit()
    {
        $this->openForm('adminmenuedit');
        $this->hiddenField('edit_admin_menu_id', 'edit_admin_menu_id');
        $this->inputRow('edit_admin_menu_name', 'edit_admin_menu_name', 'Name');
        $this->inputRow('edit_admin_menu_link', 'edit_admin_menu_link', 'Link');
        $this->inputRow('edit_admin_menu_lang', 'edit_admin_menu_lang', 'Lang Key');
        $this->closeForm();
    }

    public function adminMenuNew()
    {
        $this->openForm('adminmenunew');
        $this->inputRow('new_admin_menu_name', 'new_admin_menu_name', 'Name');
        $this->inputRow('new_admin_menu_link', 'new_admin_menu_link', 'Link');
        $this->inputRow('new_admin_menu_lang', 'new_admin_menu_lang', 'Lang Key');
        $this->inputRow('new_admin_menu_sort', 'new_admin_menu_sort', 'Sort', 'number', '0');
        $this->closeForm();
    }

    // CSS / JS / Meta
    public function addCss()
    {
        $this->openForm('add-css-modal');
        $this->inputRow('addcss', 'css', $this->lang('css_name', 'CSS name'));
        $this->selectPageRow('add_page_selection_css', 'addpage', 'addpage');
        $this->closeForm();
    }

    public function editCss()
    {
        $this->openForm('add-css-modal');
        $this->hiddenField('css_id', 'css');
        $this->inputRow('editcss', 'css', $this->lang('css_name', 'CSS name'));
        $this->selectPageRow('edit_page_selection_css', 'editpage', 'editpage');
        $this->closeForm();
    }

    public function addjs()
    {
        $this->openForm('addnewjs');
        $this->inputRow('addjs', 'addjs', $this->lang('add_js', 'Add JS'), 'text', '', 'js');
        $this->selectPageRow('add_page_selection_js', 'addnewpage', 'addpage');
        $this->closeForm();
    }

    public function editjs()
    {
        $this->openForm('addeditjs');
        $this->hiddenField('editjsid', 'editjsid');
        $this->inputRow('editjs', 'editjs', $this->lang('edit_js', 'Edit JS'), 'text', '', 'js');
        $this->selectPageRow('edit_page_selection_js', 'editnewpage', 'editpage');
        $this->closeForm();
    }

    public function editMeta()
    {
        $this->openForm('edit-meta-form');
        $this->hiddenField('meta_id', 'meta_id');
        $this->inputRow('meta_name', 'meta_name', $this->lang('meta_name', 'Meta name'));
        $this->inputRow('meta_content', 'meta_content', $this->lang('meta_content', 'Meta content'));
        $this->selectPageRow('edit_page_selection_meta', 'editpageMeta', 'editpageMeta');
        $this->closeForm();
    }

    // Shop / forum / permissions
    public function addcategories()
    {
        $this->openForm('add-cat-modal');
        $this->inputRow('cat_shop_cat', 'cat', $this->lang('add_shop_Cat', 'Add shop category'));
        $this->closeForm();
    }

    public function editcategories()
    {
        $this->openForm('edit-cat-modal');
        $this->inputRow('edit_cat_shop_cat', 'editcat', $this->lang('edit_shop_Cat', 'Edit shop category'));
        $this->closeForm();
    }

    public function addbrand()
    {
        $this->openForm('add-brand-modal');
        $this->inputRow('add_shop_brand', 'cat', $this->lang('add_shop_Brand', 'Add shop brand'));
        $this->closeForm();
    }

    public function editbrand()
    {
        $this->openForm('edit-cat-modal');
        $this->inputRow('edit_shop_brand', 'edit_shop_brand', $this->lang('edit_shop_brand', 'Edit shop brand'));
        $this->closeForm();
    }

    public function ForumCategory()
    {
        $this->openForm('add-cat-modal');
        $this->hiddenField('css_id', 'css');
        $this->inputRow('cat_name', 'cat', 'New Category');
        $this->closeForm();
    }

    public function ForumSection()
    {
        $this->openForm('add-section-modal');
        $this->hiddenField('cat_id', 'css');
        $this->inputRow('section_name', 'section', 'New Section');
        echo '<div class="form-group">';
        echo '  <label class="control-label col-lg-3" for="category_selector">Select a Category</label>';
        echo '  <div class="col-lg-9"><select id="category_selector" class="form-control">';
        if ($this->forum && method_exists($this->forum, 'category_selector')) {
            $this->forum->category_selector();
        }
        echo '  </select></div>';
        echo '</div>';
        $this->closeForm();
    }

    public function ForumEditCategory()
    {
        $this->openForm('add-section-modal');
        $this->hiddenField('cat_id', 'css');
        $this->inputRow('section_name', 'section', 'New Section');
        $this->closeForm();
    }

    public function ForumEditSection()
    {
        $this->openForm('add-section-modal');
        $this->hiddenField('cat_id', 'css');
        $this->inputRow('section_name', 'section', 'New Section');
        $this->closeForm();
    }

    public function createPerm()
    {
        $this->openForm('create_perm');
        $this->inputRow('perm_name', 'perm_name', $this->lang('permission', 'Permission'), 'text', '', 'Permission name');
        $this->closeForm();
    }

    public function kitchenSection() { $this->optionList('wipos_product_sections'); }
    public function FoodSection() { $this->optionList('wi_food_sections'); }

    // Upload-heavy legacy POS forms retained, with inline styles removed from modal shell only
    public function change()
    {
        echo '<form class="form-horizontal wi-modal-form" id="change_menu_photo" enctype="multipart/form-data" method="POST">';
        echo '  <input type="hidden" id="change_menu_pic" value="">';
        echo '  <div id="changedragandrophandler" class="menuChange">Drag &amp; Drop Files Here</div>';
        echo '  <div id="supload" value="menu"></div>';
        echo '  <div id="menuItem"></div>';
        echo '  <br><br><div id="status1"></div><hr><div id="upload-preview"></div>';
        echo '</form>';
    }

    public function photo()
    {
        echo '<form class="form-horizontal wi-modal-form" id="add_photo" enctype="multipart/form-data" method="POST">';
        echo '  <div id="menudragandrophandler">Drag &amp; Drop Files Here</div><div id="menuupload" value="addmenu"></div>';
        echo '  <div id="newMenuItem"></div>';
        echo '  <br><br><div id="status1"></div><hr><div id="upload-preview"></div>';
        echo '</form>';
    }

    public function addSite()
    {
        $this->openForm('addSite');
        $this->inputRow('site_location', 'site_location', $this->lang('site_location', 'Site location'));
        $this->inputRow('site_name', 'site_name', $this->lang('site_name', 'Site name'));
        $this->closeForm();
    }

    public function addQuest()
    {
        $this->openForm('addquest');
        $this->inputRow('question', 'question', $this->lang('question', 'Question'));
        $this->closeForm();
        echo '<a href="javascript:void(0);" class="btn btn-primary" onclick="WICompliance.addquest();">Save</a>';
    }

    public function addGroup()
    {
        $this->openForm('group_name');
        $this->inputRow('group_name', 'group_name', $this->lang('group_name', 'Group name'));
        $this->closeForm();
    }

    public function add()
    {
        echo '<div class="alert alert-info">Legacy add() modal retained for module compatibility. Refactor this specific module next before removing it.</div>';
    }

    public function editMenu()
    {
        echo '<div class="alert alert-info">Legacy editMenu() modal retained for module compatibility. Use menuEdit() for the current Settings → Menu flow.</div>';
    }

    public function addProduct()
    {
        echo '<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">';
        echo '  <div id="contact-card" class="panel panel-default">';
        echo '    <div class="panel-heading">';
        echo '      <h2 class="panel-title"><input id="newItem" type="text" name="menu_name" placeholder=""></h2>';
        echo '      <select id="menuSection">';
        $this->FoodSection();
        echo '      </select>';
        echo '      <div id="dbstatus"></div>';
        echo '    </div>';
        echo '    <div class="panel-body">';
        echo '      <div id="card" class="row">';
        echo '        <div class="col-md-6 col-lg-6 col-sm-4 col-xs-4 headshot" id="newmenuItemPic">';
        echo '          <div id="product_pic0"><img class="profile" src="WIMedia/Img/pos/products/default.jpg" width="218" alt=""></div>';
        echo '          <a href="javascript:void(0);" onclick="WIMedia.changePic(`product-edit`)" class="btn pic">' . $this->esc($this->lang('change_pic', 'Change picture')) . '</a>';
        echo '          <div id="status"></div>';
        echo '        </div>';
        echo '        <div class="col-md-6 col-lg-6 col-sm-8 col-xs-8">';
        echo '          <label for="price">' . (defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '£') . ' Price</label>';
        echo '          <input id="price" type="text" value="" placeholder="price" class="form-control">';
        echo '          <label for="desc">Description</label>';
        echo '          <input id="desc" type="text" class="form-control">';
        echo '        </div>';
        echo '      </div>';
        echo '      <a href="javascript:void(0);" class="btn btn-primary" onclick="WIProduct.AddNewItem();">Save</a>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }
}
