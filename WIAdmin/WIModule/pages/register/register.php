<?php
declare(strict_types=1);

class register
{
    private WISite $site;
    private WIModules $mod;
    private WIPage $page;
    private WIBootStrap $Boot;
    private WILogin $login;
    private WIRegister $register;

    public function __construct()
    {
       $this->site = new WISite();
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
        <section class="wi-auth-page wi-auth-register">
            <div class="wi-auth-shell">
                <div class="wi-auth-card">
                    <h2>Create account</h2>
                    <p>Complete the form below to register your account.</p>

                    <form class="register-form" method="post" action="#" data-ajax="WICore/WIClass/WIAjax.php" novalidate>
                        <fieldset>
                            ' . WIToken::csrfField('register') . '

                            <div class="wi-auth-grid">
                                <div class="form-group">
                                    <label for="reg-email">Email address</label>
                                    <input type="email" class="form-control" id="reg-email" name="email" autocomplete="email" required>
                                </div>

                                <div class="form-group">
                                    <label for="reg-username">Username</label>
                                    <input type="text" class="form-control" id="reg-username" name="username" autocomplete="username" required>
                                </div>
                            </div>

                            <div class="wi-auth-grid">
                                <div class="form-group">
                                    <label for="reg-password">Password</label>
                                    <input type="password" class="form-control" id="reg-password" name="password" autocomplete="new-password" required>
                                </div>

                                <div class="form-group">
                                    <label for="reg-repeat-password">Confirm password</label>
                                    <input type="password" class="form-control" id="reg-repeat-password" name="confirm_password" autocomplete="new-password" required>
                                </div>
                            </div>

                            <button type="submit" id="btn-register" class="wi-auth-submit">
                                Create secure account
                            </button>

                            <div class="wi-auth-footer">
                                Already have an account? <a href="login.php">Sign in</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </section>
        <script src="WICore/WIJ/WICore.js"></script>
        <script src="WICore/WIJ/WIRegister.js"></script>';

        $this->Boot->endContentsHolder();
        $this->Boot->endMod($page);
    }
}