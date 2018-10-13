<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

$page_title = 'Register';

// Add a random nonce to the form. Helps prevent cross-site request forgeries
// $nonce = bin2hex(openssl_random_pseudo_bytes(16));
// $_SESSION['register_nonce'] = $nonce;

$action = "/register";
$username = $password = $password_conf = $email = "";

$errors = [];

while ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(@$_POST['username']);
    $password = trim(@$_POST['password']);
    $password_conf = trim(@$_POST['password-confirm']);
    $email = trim(@$_POST['email']);
}

if (isset($_SESSION['register_reload']) && $_SESSION['register_reload']) {
    $username = htmlspecialchars($_SESSION['username']);
    $password = htmlspecialchars($_SESSION['password']);
    $password_conf = htmlspecialchars($_SESSION['password-confirm']);
    $email = htmlspecialchars($_SESSION['email']);
    $_SESSION['register_reload'] = false;
    $validate_now =<<<END_VALIDATE
<script>
$(function(){
    checkUserName();
    checkPassword();
    checkPasswordConfirm();
    checkEmail();
});
</script>

END_VALIDATE;
}

$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1>🔒Priv8Chat</h1>
    <p>New User Registration</p>
  </div>
  <div class="container">
    <div class="col-sm-12">
      <p>To create a new account, just fill in the form below and check your email for a
        confirmation link. Please observe the following guidelines:</p>
      <ul style="font-size: 0.875em">
        <li><strong>User name:</strong> Up to 64 alphabet characters, digits and underscores.
          Not case-sensitive.</li>
        <li><strong>Email address:</strong> If you'd rather not use your own email address,
          use a free mailbox at <a href="//www.sharklasers.com/">sharklasers.com</a>
          instead. But don't forget it, otherwise you won't be able to reset your pass
          phrase.</li>
        <li><strong>Pass phrase:</strong> Please choose a strong pass phrase, but avoid
          using non-ASCII characters like emojis and accented characters, which can cause
          problems in some browsers. Case sensitive.  If you like, you can use one of the
          following buttons to generate a random pass phrase:
          <ul class="mt-2">
            <li><a class="btn-like btn btn-sm btn-success mr-2"
                   style="min-width:6em; padding: .125rem .25rem;"
                   href="javascript:"
                   onclick="return random_memorable()" tabindex="3">Memorable</a>
              (a four-word phrase that should be fairly easy to remember)</li>
            <li><a class="btn-like btn btn-sm btn-warning mr-2"
                   style="min-width:6em; padding: .125rem .25rem;"
                   href="javascript:"
                   onclick="return random_short()" tabindex="4">Random</a>
              (a random alphanumeric password; short, but less memorable)</li>
            <li><a class="btn-like btn btn-sm btn-danger mr-2"
                   style="min-width:6em; padding: .125rem .25rem;"
                   href="javascript:"
                   onclick="return random_extreme()" tabindex="4">Extreme</a>
              (evil, but perhaps OK if you're using a password manager)</li>
          </ul>
        </li>
      </ul>
    </div>
    <div class="col-sm-12">
    <div class="well">
        <form id="regform" class="regform" autocomplete="off" action="$action"
              method="POST" onsubmit="return checkRegForm()">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="username">User name</label>
                    <div class="input-group">
                        <input type="text" value="$username" class="form-control"
                               id="username" name="username" maxlength="64"
                               pattern="[A-Za-z0-9.:_-]{3,64}" tabindex="1"
                               onchange="checkUserName()" required autofocus>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-name"
                                  style="width:1em; height:1em;" data="/img/blank.svg"
                                  type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-name" class="feedback">&nbsp;</div>
                </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="email">Email address</label>
                    <div class="input-group">
                        <input type="email" value="$email" class="form-control"
                               id="email" name="email" maxlength="80" tabindex="6"
                               onchange="checkEmail()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-email"
                                  style="width:1em; height:1em;" data="/img/blank.svg"
                                  type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-email" class="feedback">&nbsp;</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="password">Pass phrase</label> &nbsp; <a id="passwd-toggle"
                           href="javascript:void(0)" onclick="return togglePasswordReveal();"
                           data-state="hide">(Reveal)</a>
                    <div class="input-group">
                        <input type="password" value="$password" class="form-control"
                               id="password" name="password" maxlength="200"
                               autocomplete="new-password" tabindex="2"
                               onchange="checkPassword()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-pass"
                                  style="width:1em; height:1em;" data="/img/blank.svg"
                                  type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-password" class="feedback">&nbsp;</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="password-confirm">Confirm pass phrase</label>
                    <div class="input-group">
                        <input type="password" value="$password_conf" class="form-control"
                               id="password-confirm" name="password-confirm"
                               onpaste="return false" maxlength="1000"
                               autocomplete="new-password" tabindex="5"
                               onchange="checkPasswordConfirm()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-conf"
                            style="width:1em; height:1em;" data="/img/blank.svg"
                            type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-password-confirm" class="feedback">&nbsp;</div>
                </div>
            </div>
            <div class="form-row mt-3">
                <div class="col-sm-12 text-center">
                <button class="btn btn-primary" type="submit" id="regsub">Submit form</button>
            </div>
        </form>
      </div>
    </div>
  </div>

END_PAGE;

include "$ROOT/views/main.php";
