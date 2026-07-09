<?php
declare(strict_types=1);

class login
{
    private WISite $site;
    private WIModules $mod;
    private WIPage $page;
    private WIBootStrap $Boot;
    private WILogin $login;

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
        <section class="wi-auth-page wi-auth-login">
            <div class="wi-auth-shell">
                <div class="wi-auth-card">
                    <h2>Sign in</h2>
                    <p>Enter your username and password to continue.</p>

                    <form class="form-horizontal wi-auth-form" method="post" action="#" data-ajax="WICore/WIClass/WIAjax.php" novalidate>
                        ' . WIToken::csrfField('login') . '

                        <div class="form-group">
                            <label for="login-username">Username</label>
                            <input type="text" class="form-control" id="login-username" name="username" autocomplete="username" required>
                        </div>

                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <input type="password" class="form-control" id="login-password" name="password" autocomplete="current-password" required>
                        </div>

                        <div class="wi-auth-row">
                            <a class="wi-auth-link" href="forgotten_password.php">Forgotten your password?</a>
                        </div>

                        <button type="submit" id="btn-login" class="wi-auth-submit">
                            Sign in securely
                        </button>

                        <div class="wi-auth-footer">
                            New here? <a href="register.php">Create an account</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <script src="WICore/WIJ/WICore.js"></script>
        <script src="WICore/WIJ/WILogin.js"></script>';
        
        $this->Boot->endContentsHolder();
        $this->Boot->endMod($page);
    }
}