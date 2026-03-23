<?php
$siteName   = (string) $site->Website_Info('site_name');
$siteDomain = (string) $site->Website_Info('site_domain');
$siteUrl    = (string) $site->Website_Info('site_url');
?>

<form class="form-horizontal website-settings-form" id="website-settings-form">
    <fieldset>
        <div class="settings-panel">
            <div class="settings-panel-header">
                <legend class="settings-panel-title">Website Settings</legend>
                <p class="settings-panel-subtitle">
                    Manage the main website identity and base domain settings.
                </p>
            </div>

            <div class="settings-panel-body">
                <?php echo WIToken::csrfField('wi_ajax'); ?>

                <input type="hidden" name="action" value="site_settings">

                <div class="form-group row">
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label class="control-label" for="website_name">Website Name</label>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <input
                            type="text"
                            id="website_name"
                            name="settings[UserData][site_name]"
                            maxlength="88"
                            placeholder="Website Name"
                            class="form-control"
                            value="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                        <small class="form-text text-muted">
                            This is the main display name used across the system.
                        </small>
                    </div>
                </div>

                <div class="alert alert-warning settings-warning-box">
                    <div class="form-group row">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label class="control-label" for="website_domain">Website Domain</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <input
                                type="text"
                                id="website_domain"
                                name="settings[UserData][site_domain]"
                                maxlength="100"
                                placeholder="Website Domain"
                                class="form-control"
                                value="<?php echo htmlspecialchars($siteDomain, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label class="control-label" for="website_url">Website URL</label>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <input
                                type="text"
                                id="website_url"
                                name="settings[UserData][site_url]"
                                maxlength="100"
                                placeholder="Website URL"
                                class="form-control"
                                value="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </div>
                    </div>

                    <div class="settings-help-text mt-3">
                        Changing the domain settings can affect routing, links, login flow, and core functionality.
                        These values should normally only be changed by an administrator who understands the hosting setup.
                    </div>
                </div>
            </div>

            <div class="settings-panel-footer text-right">
                <button type="submit" id="site_settings" class="btn btn-success">
                    Save Website Settings
                </button>
            </div>

            <div class="results mt-3" id="wresults"></div>
        </div>
    </fieldset>
</form>