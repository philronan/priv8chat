<?php

if (!@$VIA_INDEX) die(''); // No output unless accessed via /index.php

// include $GLOBALS['APPROOT'] . '/crypto.php';

$page_title = 'Log in';

// Invite the user to log out if already logged in
if ($_SESSION['logged_in']) {
    $page_content = <<<END_PAGE
<div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Log in</p>
</div>
<div class="container">
    <div class="col-sm-12">
    <p class="lead">You're already logged in. If you want to log in as a different user, please log out first. (Click the "Log out" link in the menu at the top of this page.)</p>
</div>

END_PAGE;
    include $GLOBALS['APPROOT'] . '/views/main.php';
    die();
}

// Add a random nonce to the form. Helps prevent cross-site request forgeries
// $nonce = bin2hex(openssl_random_pseudo_bytes(16));
// $_SESSION['register_nonce'] = $nonce;

$action = $GLOBALS['WEBROOT'] . '/login';

$errors = [];

while ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // User clicked the submit button, so let's do the registration
    $username = trim(@$_POST['username']);
    $password = trim(@$_POST['password']);
    if (strlen($username) == 0) {
        $errors[] = _("You haven't provided a username");
    }
    elseif (strlen($username > 64)) {
        $errors[] = _("Your username is too long (no more than 64 characters, please)");
    }
    elseif (preg_match('/^[A-Za-z0-9_]{1,64}$/', $username) != 1) {
        $errors[] = _("Your username can only contain alphabet characters, digits and underscores");
    }
    $username_enc = username_encrypt($username);
    $q = "SELECT * FROM priv8users WHERE username_encrypted='$username_enc';";
    $res = $mysqli->query($q);
    if ($res->num_rows < 1) {
        // Avoid timing attacks by testing a password hash anyway
        $hash = '$2y$08$get.a.password.to.match.this.and.you.win.the.internet'; // good luck :)
    }
    else {
        $userdata = $res->fetch_assoc();
        $hash = $userdata['password'];
    }
    if (sc_password_verify($password, $hash)) {
        // Hello
        $_SESSION['logged_in'] = 1;
        $_SESSION['start'] = time();
        $_SESSION['last_hit'] = time();
        $_SESSION['username'] = username_decrypt($username_enc);
        $_SESSION['user_id'] = intval($userdata['user_id']);
        $new_loc = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['WEBROOT'] . '/inbox';
        header("HTTP/1.1 303 See other");
        header("Location: $new_loc");
        die("If you had a decent browser, you wouldn't have to <a href=\"$new_loc\">click here to continue</a>.");
    }
    $errors[] = _("Either the username or password is incorrect. Please check your typing.");
    break;
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
    <p>Log in</p>
  </div>
  <div class="container">
    <div class="col-sm-12">
$error_report
      <p>Please enter the username and pass phrase you provided when you signed up.</p>
    </div>
    <div class="col-sm-12">
    <div class="well">
        <form id="loginform" class="regform" action="$action" method="POST">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="username">User name</label>
                    <div class="input-group">
                        <input type="text" class="form-control"
                               id="username" name="username" maxlength="64"
                               pattern="[A-Za-z0-9_]{1,64}" tabindex="1" required autofocus>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="password">Pass phrase</label>
                    <div class="input-group">
                        <input type="password" class="form-control"
                               id="password" name="password" maxlength="200"
                               tabindex="2" required>
                    </div>
                </div>
            </div>
            <div class="form-row mt-3">
                <div class="col-sm-12 text-center">
                <button class="btn btn-primary" type="submit">Log in</button>
            </div>
        </form>
      </div>
    </div>
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
