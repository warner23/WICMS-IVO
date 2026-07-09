<?php
declare(strict_types=1);

class forgotten_password
{
    private WISite $site;
    private WIModules $mod;
    private WIPage $page;
    private WIBootStrap $Boot;
    private WILogin $login;

    public function __construct()
    {
        $this->site = new WISite();
        $this->mod  = new WIModules();
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

        $this->Boot->startMod($page);
        $this->Boot->startContentsHolder();

        echo '
        <section class="wi-auth-page wi-auth-forgot">
            <style>
                .wi-auth-page{
                    min-height:72vh;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    padding:48px 16px;
                    background:
                        radial-gradient(circle at top left, rgba(14,165,233,0.08), transparent 30%),
                        radial-gradient(circle at bottom right, rgba(99,102,241,0.08), transparent 28%),
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
                    color:#2563eb;
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
                }
                .wi-auth-form .form-control:focus{
                    border-color:#2563eb;
                    box-shadow:0 0 0 4px rgba(37,99,235,0.12);
                    outline:none;
                }
                .wi-auth-submit{
                    width:100%;
                    min-height:54px;
                    border:0;
                    border-radius:14px;
                    background:linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
                    color:#ffffff;
                    font-size:15px;
                    font-weight:800;
                }
                .wi-auth-links{
                    margin-top:18px;
                    text-align:center;
                    font-size:14px;
                    color:#667085;
                }
                .wi-auth-links a{
                    color:#2563eb;
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
                @media (max-width: 767px){
                    .wi-auth-wrap{
                        padding:32px 22px;
                    }
                    .wi-auth-wrap h1{
                        font-size:28px;
                    }
                }
            </style>

            <div class="wi-auth-wrap">
                <h1>Reset access for <span>' . $siteName . '</span></h1>
                <p>Enter the email address associated with your account and we will start the password reset process.</p>

                <div class="wi-auth-message" id="forgot-message"></div>

                <form class="wi-auth-form" id="forgot-password-form" method="post" action="#" data-ajax="WICore/WIClass/WIAjax.php" novalidate>
                    ' . WIToken::csrfField('forgot_password') . '

                    <div class="form-group">
                        <label for="forgot-email">Email address</label>
                        <input type="email" class="form-control" id="forgot-email" name="email" autocomplete="email" required>
                    </div>

                    <button type="submit" id="btn-forgot-password" class="wi-auth-submit">Send reset request</button>

                    <div class="wi-auth-links">
                        <a href="login.php">Back to login</a> &nbsp;|&nbsp; <a href="register.php">Create account</a>
                    </div>
                </form>
            </div>
        </section>

        <script>
        $(document).ready(function () {
            $("#forgot-password-form").on("submit", function (e) {
                e.preventDefault();

                var form = $(this);
                var email = $.trim($("#forgot-email").val());
                var messageBox = $("#forgot-message");

                messageBox.removeClass("success error").hide().text("");

                if (email === "") {
                    messageBox.addClass("error").text("Email is required.").show();
                    return false;
                }

                $.ajax({
                    url: form.data("ajax") || "WICore/WIClass/WIAjax.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        action: "forgotPassword",
                        email: email,
                        csrf_token: form.find(\'input[name="csrf_token"]\').val()
                    },
                    success: function (result) {
                        if (result && result.status === "success") {
                            messageBox.addClass("success").text(result.message || "Password reset request sent.").show();
                            form[0].reset();
                            return;
                        }

                        messageBox.addClass("error").text((result && result.message) ? result.message : "Unable to process request.").show();
                    },
                    error: function (xhr) {
                        var message = "Something went wrong. Please try again.";

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