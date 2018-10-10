<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

$page_title = 'Register';

// Add a random nonce to the form. Helps prevent cross-site request forgeries
$nonce = bin2hex(openssl_random_pseudo_bytes(16));
$_SESSION['register_nonce'] = $nonce;

$action = '/handlers/register.php';
$username = $password = $password_conf = $email = $validate_now = "";
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
  <script>
    var nonce = "$nonce";
  </script>
  <div class="jumbotron text-center">
    <h1>🔒<svg viewBox="0 0 10 10" style="width:1em; height:1em"><path d="M0 0 10 0 10 10 0 10Z"/></svg>Priv8Chat</h1>
    <p>New User Registration</p>
  </div>
  <div class="container">
    <div class="col-sm-12">
        <p>To create a new account, just fill in the form below and check your email for a confirmation link. Please
          observe the following guidelines:</p>
        <ul style="font-size: 0.875em">
          <li><strong>User name:</strong> Should contain at least 3 characters, and no more than 64, consisting of alphabet
            characters (<code>a-z</code>, <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>). If you
            don't like receiving spam and other unsolicited messages, choose a name that is difficult to guess (e.g., instead
            of <code>dave</code>, try something like <code>david_eric_grohl_69</code>). User names are not case-sensitive.</li>
          <li><strong>Email address:</strong> If you'd rather not share your personal email address, that's fine. Just use a
            temporary anonymous mailbox at <a href="https://www.guerrillamail.com/">guerrillamail.com</a> instead. But note
            it down somewhere; you'll need it if you ever want to reset your password.</li>
          <li><strong>Pass phrase:</strong> You can enter pretty much anything here as long as it is sufficiently secure.
            However, you might want to avoid emojis and special characters like ® and µ, as these can cause problems in some
            browsers. Unlike your user name, the pass phrase is <strong>case sensitive</strong>.  If you like, you can use
            one of the following buttons to generate a password automatically:
            <ul class="mt-2">
              <li><a class="btn-like btn btn-sm btn-success mr-2" style="min-width:6em; padding: .125rem .25rem;" href="javascript:" onclick="passwordShow(); do { \$('#password').val(memorablePassphrase(4)); } while (zxcvbn($('#password').val(), [$('#username').val()]).score < 4); checkPassword(); return false" tabindex="3">Memorable</a> (a four-word phrase that should be fairly easy to remember)</li>
              <li><a class="btn-like btn btn-sm btn-warning mr-2" style="min-width:6em; padding: .125rem .25rem;" href="javascript:" onclick="passwordShow(); do { \$('#password').val(randomPassword(12)); } while (zxcvbn($('#password').val(), [$('#username').val()]).score < 4); checkPassword(); return false" tabindex="4">Random</a> (a random alphanumeric password; short, but less memorable)</li>
              <li><a class="btn-like btn btn-sm btn-danger mr-2" style="min-width:6em; padding: .125rem .25rem;" href="javascript:" onclick="passwordShow(); do { \$('#password').val(strongPassword()); } while (zxcvbn($('#password').val(), [$('#username').val()]).score < 4); checkPassword(); return false" tabindex="4">Extreme</a> (evil, but perhaps OK if you're using a password manager)</li>
            </ul>
          </li>
        </ul>
    </div>
    <div class="col-sm-12">
    <div class="well">
        <form id="regform" class="regform" autocomplete="off" action="$action" method="POST" onsubmit="return checkRegForm()">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="username">User name</label>
                    <div class="input-group">
                        <input type="text" value="$username" class="form-control" id="username" name="username" maxlength="64" pattern="[A-Za-z0-9.:_-]{6,64}" tabindex="1" onchange="checkUserName()" required autofocus>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-name" style="width:1em; height:1em;" data="/img/blank.svg" type="image/svg+xml">...</object></span>
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
                        <input type="email" value="$email" class="form-control" id="email" name="email" maxlength="80" tabindex="6" onchange="checkEmail()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-email" style="width:1em; height:1em;" data="/img/blank.svg" type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-email" class="feedback">&nbsp;</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="password">Pass phrase</label> &nbsp; <a id="passwd-toggle" href="javascript:void(0)" onclick="return togglePasswordReveal();" data-state="hide">(Reveal)</a>
                    <div class="input-group">
                        <input type="password" value="$password" class="form-control" id="password" name="password" maxlength="200" autocomplete="new-password" tabindex="2" onchange="checkPassword()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-pass" style="width:1em; height:1em;" data="/img/blank.svg" type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-password" class="feedback">&nbsp;</div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="password-confirm">Confirm pass phrase</label>
                    <div class="input-group">
                        <input type="password" value="$password_conf" class="form-control" id="password-confirm" name="password-confirm" onpaste="return false" maxlength="1000" autocomplete="new-password" tabindex="5" onchange="checkPasswordConfirm()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-conf" style="width:1em; height:1em;" data="/img/blank.svg" type="image/svg+xml">...</object></span>
                        </div>
                    </div>
                    <div id="feedback-password-confirm" class="feedback">&nbsp;</div>
                </div>
            </div>
            <div class="form-row mt-3">
                <div class="col-sm-12 text-center">
                <input type="hidden" name="nonce" value="$nonce">
                <button class="btn btn-primary" type="submit" id="regsub">Submit form</button>
            </div>
        </form>
      </div>
    </div>
  </div>
$validate_now
END_PAGE;

include "$ROOT/views/main.php";
