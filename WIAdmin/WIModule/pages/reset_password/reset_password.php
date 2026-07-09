<?php
declare(strict_types=1);
/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: reset_password.php
| Location: /WIAdmin/WIModule/pages/reset_password/
| Type: PHP Module
| Layer: Front-Side Auth UI
| Purpose Area: Password reset form
| Version: 2.1.0
| Created: Legacy
| Last Updated: 2026-06-21
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Canonical reset-password module. Accepts both key and k query parameters,
| validates the reset key before showing the form, and posts to the root WICMS
| AJAX dispatcher.
*/

class reset_password
{
    private WISite $site;
    private WIModules $mod;
    private WIPage $page;
    private WIBootStrap $Boot;
    private WILogin $login;

    public function __construct()
    {
        $this->site = new WISite();
        $this->mod = new WIModules();
        $this->page = new WIPage();
        $this->Boot = new WIBootStrap();
        $this->login = new WILogin();
    }

    public function mod_name($page): void
    {
        if ($this->login->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }

        $siteName = htmlspecialchars((string) $this->site->Website_Info('site_name'), ENT_QUOTES, 'UTF-8');
        $resetKey = trim((string) ($_GET['key'] ?? $_GET['k'] ?? ''));
        $safeKey = htmlspecialchars($resetKey, ENT_QUOTES, 'UTF-8');

        $keyIsValid = false;

        if ($resetKey !== '' && class_exists('WIValidator')) {
            try {
                $validator = new WIValidator();
                $keyIsValid = $validator->prKeyValid($resetKey);
            } catch (Throwable $e) {
                $keyIsValid = false;
            }
        }

        $this->Boot->startMod($page);
        $this->Boot->startContentsHolder();

        echo '
        <section class="wi-auth-page wi-auth-reset">
            <style>
                .wi-auth-page{
                    min-height:72vh;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    padding:48px 16px;
                    background:
                        radial-gradient(circle at top left, rgba(16,185,129,0.08), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(59,130,246,0.08), transparent 28%),
                        linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
                }
                .wi-auth-wrap{
                    width:100%;  
                    max-width:760px;
                    background:#ffffff;
                    border:1px solid #e5e7eb;
                    border-radius:24px;
                    box-shadow:0 20px 60px rgba(15, 23, 42, 0.10);
                    padding:48px;
                }
                .wi-auth-wrap h1{
                    margin:0 0 10px;
                    font-size:34px;
                    font-weight:800;
                    color:#0f172a;
                }
                .wi-auth-wrap h1 span{
                    color:#059669;
                }
                .wi-auth-wrap p{
                    margin:0 0 26px;
                    color:#475467;
                    line-height:1.7;
                    font-size:15px;
                }
                .wi-auth-form .form-group{
                    margin-bottom:18px;
                }
                .wi-auth-form label{
                    display:block;
                    margin-bottom:8px;
                    font-size:13px;
                    font-weight:700;
                    color:#344054;
                }
                .wi-auth-form .form-control{
                    width:100%;
                    min-height:52px;
                    border-radius:14px;
                    border:1px solid #d0d5dd;
                    padding:14px 16px;
                    font-size:15px;
                    box-sizing:border-box;
                }
                .wi-auth-form .form-control:focus{
                    border-color:#059669;
                    box-shadow:0 0 0 4px rgba(5,150,105,0.12);
                    outline:none;
                }
                .wi-auth-submit{
                    width:100%;
                    min-height:54px;
                    border:0;
                    border-radius:14px;
                    background:linear-gradient(135deg, #10b981 0%, #2563eb 100%);
                    color:#ffffff;
                    font-size:15px;
                    font-weight:800;
                    cursor:pointer;
                }
                .wi-auth-submit:disabled{
                    opacity:.7;
                    cursor:not-allowed;
                }
                .wi-auth-links{
                    margin-top:18px;
                    text-align:center;
                    font-size:14px;
                    color:#667085;
                }
                .wi-auth-links a{
                    color:#059669;
                    font-weight:700;
                    text-decoration:none;
                }
                .wi-auth-message{
                    display:none;
                    margin-bottom:18px;
                    padding:14px 16px;
                    border-radius:14px;
                    font-size:14px;
                    line-height:1.6;
                }
                .wi-auth-message.success{
                    display:block;
                    background:#ecfdf3;
                    border:1px solid #abefc6;
                    color:#067647;
                }
                .wi-auth-message.error{
                    display:block;
                    background:#fef3f2;
                    border:1px solid #fecdca;
                    color:#b42318;
                }
                .wi-auth-invalid{
                    display:block;
                    background:#fff7ed;
                    border:1px solid #fed7aa;
                    color:#9a3412;
                    padding:16px 18px;
                    border-radius:16px;
                    line-height:1.7;
                    margin:22px 0;
                }
                @media (max-width: 767px){
                    .wi-auth-page{
                        padding:24px 10px;
                    }
                    .wi-auth-wrap{
                        padding:28px 18px;
                        border-radius:18px;
                    }
                    .wi-auth-wrap h1{
                        font-size:26px;
                    }
                    .wi-auth-form .form-control{
                        min-height:56px;
                        font-size:16px;
                    }
                }
            </style>

            <div class="wi-auth-wrap">
                <h1>Choose a new password for <span>' . $siteName . '</span></h1>';

        if (!$keyIsValid) {
            echo '
                <div class="wi-auth-invalid">
                    This password reset link is missing, invalid, already used, or has expired.
                    Please request a fresh reset link.
                </div>

                <div class="wi-auth-links">
                    <a href="forgotpass.php">Request a new reset link</a> &nbsp;|&nbsp; <a href="login.php">Back to login</a>
                </div>
            </div>
        </section>';

            $this->Boot->endContentsHolder();
            $this->Boot->endMod($page);
            return;
        }

        echo '
                <p>Set a strong new password to restore secure access to your account.</p>

                <div class="wi-auth-message" id="reset-message"></div>

                <form class="wi-auth-form" id="reset-password-form" method="post" action="#" data-ajax="WICore/WIClass/WIAjax.php" novalidate>
                    ' . WIToken::csrfField('reset_password') . '
                    <input type="hidden" id="reset-key" name="key" value="' . $safeKey . '">

                    <div class="form-group">
                        <label for="reset-password">New password</label>
                        <input type="password" class="form-control" id="reset-password" name="newPass" autocomplete="new-password" required>
                    </div>

                    <div class="form-group">
                        <label for="reset-password-confirm">Confirm new password</label>
                        <input type="password" class="form-control" id="reset-password-confirm" name="confirm_newPass" autocomplete="new-password" required>
                    </div>

                    <button type="submit" id="btn-reset-password" class="wi-auth-submit">Update password</button>

                    <div class="wi-auth-links">
                        <a href="login.php">Back to login</a>
                    </div>
                </form>
            </div>
        </section>

        <script>
        $(document).ready(function () {
            $("#reset-password-form").on("submit", function (e) {
                e.preventDefault();

                var form = $(this);
                var password = $("#reset-password").val();
                var confirmPassword = $("#reset-password-confirm").val();
                var key = $("#reset-key").val();
                var messageBox = $("#reset-message");
                var submitButton = $("#btn-reset-password");

                messageBox.removeClass("success error").hide().text("");

                if ($.trim(password) === "") {
                    messageBox.addClass("error").text("Password is required.").show();
                    return false;
                }

                if (password.length < 8) {
                    messageBox.addClass("error").text("Password must be at least 8 characters.").show();
                    return false;
                }

                if (password !== confirmPassword) {
                    messageBox.addClass("error").text("Passwords do not match.").show();
                    return false;
                }

                if ($.trim(key) === "") {
                    messageBox.addClass("error").text("Missing reset key.").show();
                    return false;
                }

                submitButton.prop("disabled", true).text("Updating...");

                $.ajax({
                    url: form.data("ajax") || "WICore/WIClass/WIAjax.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        action: "resetPassword",
                        newPass: password,
                        key: key,
                        csrf_token: form.find(\'input[name="csrf_token"]\').val()
                    },
                    success: function (result) {
                        if (result && result.status === "success") {
                            messageBox.addClass("success").text(result.message || "Password reset complete. You can now log in.").show();
                            form[0].reset();
                            setTimeout(function () {
                                window.location.href = "login.php";
                            }, 1200);
                            return;
                        }

                        submitButton.prop("disabled", false).text("Update password");
                        messageBox.addClass("error").text((result && result.message) ? result.message : "Unable to reset password.").show();
                    },
                    error: function (xhr) {
                        var message = "Something went wrong. Please try again.";

                        submitButton.prop("disabled", false).text("Update password");

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        messageBox.addClass("error").text(message).show();
                    }
                });

                return false;
            });
        });
        </script>';

        $this->Boot->endContentsHolder();
        $this->Boot->endMod($page);
    }
}