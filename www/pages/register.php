<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

include $GLOBALS['APPROOT'] . '/crypto.php';

$page_title = 'Register';

function randomPassword($numChars) {
    $alphabet = "256789bcdfghjkmnpqrstvwxz";
    $pw = "";
    for ($i=0; $i<$numChars; $i++) {
        do {
            $rnd = ord(openssl_random_pseudo_bytes(1)) & 0x1f;
        } while ($rnd >= strlen($alphabet));
        $pw .= substr($alphabet, $rnd, 1);
        // Add hyphen after every 4 chars if numChars is a multiple of 4
        if ($numChars%4 == 0 && $i%4 == 3 && $i<$numChars-1) $pw .= '-';
    }
    return $pw;
}

function random_token() {
    $alphabet = "256789BCDFGHJKMNPQRSTVWXZbcdfghjkmnpqrstvwxz";
    $tok = "";
    for ($i=0; $i<24; $i++) {
        do {
            $rnd = ord(openssl_random_pseudo_bytes(1)) & 0x3f;
        } while ($rnd >= strlen($alphabet));
        $tok .= substr($alphabet, $rnd, 1);
    }
    return $tok;
}


// Add a random nonce to the form. Helps prevent cross-site request forgeries
// $nonce = bin2hex(openssl_random_pseudo_bytes(16));
// $_SESSION['register_nonce'] = $nonce;

$action = "/register";
$username = $password = $password_conf = $email = "";

$errors = [];

while ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // User clicked the submit button, so let's do the registration
    // First validate the input
    include($GLOBALS['APPROOT'] . '/zxcvbn-loader.php');
    $username = trim(@$_POST['username']);
    $password = trim(@$_POST['password']);
    $password_conf = trim(@$_POST['password-confirm']);
    $email = trim(@$_POST['email']);
    if (strlen($username) == 0) {
        $errors[] = _("You haven't provided a username");
    }
    elseif (strlen($username > 64)) {
        $errors[] = _("Your username is too long (no more than 64 characters, please)");
    }
    elseif (preg_match('/^[A-Za-z0-9_]{1,64}$/', $username) != 1) {
        $errors[] = _("Your username can only contain alphabet characters, digits and underscores");
    }
    else {
        $username_enc = username_encrypt($username);
        $last_week = date("Y-m-d H:i:s", time() - 86400*7);
        // Purge non-confirmed accounts
        $mysqli->query("DELETE FROM priv8users WHERE conf_link_sent<'$last_week' AND conf_link_clicked=0;");
        $res = $mysqli->query("SELECT * FROM priv8users WHERE username_encrypted = '$username_enc';");
        if ($res->num_rows > 0) {
            $errors[] = _("That username has already been taken, sorry");
        }
    }
    if (strlen($email) == 0) {
        $errors[] = _("You haven't provided an email address");
    }
    elseif (preg_match('/^[^@\s]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/', $email) != 1) {
        $errors[] = _("You have provided an invalid email address. Please check your typing");
    }
    else {
        $email_hsh = email_hash($email);
        $res = $mysqli->query("SELECT * FROM priv8users WHERE email_hashed = '$email_hsh';");
        if ($res->num_rows > 0) {
            $errors[] = _("There is already an account with that email address. Try logging in instead.");
        }
    }
    if (strlen($password) == 0 && strlen($password_conf) == 0) {
        $errors[] = _("You haven't provided a password");
    }
    elseif (strcmp($password, $password_conf) != 0) {
        $errors[] = _("The passwords you provided don't match");
    }
    elseif (!is_strong_password($password, [$username, $email, strrev($username), strrev($email), $_SERVER['HTTP_HOST']])) {
        $errors[] = _("Please choose a stronger password");
    }

    if (count($errors) > 0) break;

    include "$APPROOT/confirmation-email.php";
    // If the user data is valid, create a new account
    $pwh = sc_password_hash($password);
    $q = "INSERT INTO priv8users (username_encrypted,email_hashed,password) " .
         "VALUES ('$username_enc', '$email_hsh', '$pwh');";

    // An error here probably means we tried to create two accounts with the
    // same email address (checked above, but maybe another process is doing
    // the same thing at the same time?)
    if (!$mysqli->query($q)) {
        $errors[] = _("Sorry, there was a problem with the server. Please try again later");
        break;
    }
    $user_id = $mysqli->insert_id;

    // Generate a random token, and store its SHA256 hash in the database
    // Send the token to the user in a confirmation link. If the hash
    // value of this tokem matches the stored value, then the account is
    // activated
    $token = randomPassword(24);
    $token_hash = base64_encode(openssl_digest($token, 'SHA256', TRUE));
    if (!signup_email($username, $email, $token)) {
        $mysqli->query("DELETE FROM priv8users WHERE user_id=$user_id");
        $errors[] = _("Sorry, but I wasn't able to send a confirmation email. Please try again later");
        break;
    }
    $q = "UPDATE priv8users SET conf_link_sent=CURRENT_TIMESTAMP, " .
         "conf_link_token='$token_hash' WHERE user_id=$user_id;";
    $mysqli->query($q);
    $new_loc = 'https://' . $_SERVER['HTTP_HOST'] . $GLOBALS['WEBROOT'] . '/welcome';
    header("HTTP/1.1 303 See other");
    header("Location: $new_loc");
    die("If you had a decent browser, you wouldn't have to <a href=\"$new_loc\">click here to continue</a>.");



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

// Expand error messages if present
$error_report = "";
if (count($errors) > 0) {
    $error_report = "<div class=\"alert alert-danger\">\n<ul>\n<p>";
    $error_report .= _("Sorry, but you need to fix the following error(s):");
    $error_report .= "</p>\n<ul>\n<li>";
    $error_report .= implode("</li>\n<li>", $errors);
    $error_report .= "</li>\n</ul>\n</div>";
}

$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>New User Registration</p>
  </div>
  <div class="container">
    <div class="col-sm-12">
$error_report
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
                   onclick="return random_memorable()">Memorable</a>
              (a four-word phrase that should be fairly easy to remember)</li>
            <li><a class="btn-like btn btn-sm btn-warning mr-2"
                   style="min-width:6em; padding: .125rem .25rem;"
                   href="javascript:"
                   onclick="return random_short()">Random</a>
              (a random alphanumeric password; short, but less memorable)</li>
            <li><a class="btn-like btn btn-sm btn-danger mr-2"
                   style="min-width:6em; padding: .125rem .25rem;"
                   href="javascript:"
                   onclick="return random_extreme()">Extreme</a>
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
                               pattern="[A-Za-z0-9_]{1,64}" tabindex="1"
                               onchange="checkUserName()" required autofocus>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-name"
                                  style="width:1em; height:1em;" data="/img/blank.svg"
                                  type="image/svg+xml" tabindex="-1">...</object></span>
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
                               id="email" name="email" maxlength="80"
                               onchange="checkEmail()" tabindex="2" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-email"
                                  style="width:1em; height:1em;" data="/img/blank.svg"
                                  type="image/svg+xml" tabindex="-1">...</object></span>
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
                               autocomplete="new-password" tabindex="3"
                               onchange="checkPassword()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-pass"
                                  style="width:1em; height:1em;" data="/img/blank.svg"
                                  type="image/svg+xml" tabindex="-1">...</object></span>
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
                               autocomplete="new-password" tabindex="4"
                               onchange="checkPasswordConfirm()" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><object id="spin-conf"
                            style="width:1em; height:1em;" data="/img/blank.svg"
                            type="image/svg+xml" tabindex="-1">...</object></span>
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

include $GLOBALS['APPROOT'] . '/views/main.php';
