<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php
include $GLOBALS['APPROOT'] . '/crypto.php';

$username = strtolower(@$_GET['name']);
$token = @$_GET['token'];

$accept = TRUE;

if (preg_match('/^[a-z0-9_]{1,64}$/', $username) != 1 ||
    preg_match('/^[A-Za-z0-9-]{9,99}$/', $token) != 1) {
    $accept = FALSE;
}
else {
    $name_enc = username_encrypt($username);
    $token_hash = base64_encode(openssl_digest($token, 'SHA256', TRUE));
    $last_week = date("Y-m-d H:i:s", time() - 86400*7);
    $q = "SELECT * FROM priv8users WHERE username_encrypted='$name_enc' AND " .
         "conf_link_token='$token_hash' AND conf_link_sent > '$last_week';";
    $res = $mysqli->query($q);
    if ($res->num_rows != 1) {
        $accept = FALSE;
    }
    else {
        $row = $res->fetch_assoc();
        $user_id = $row['user_id'];
        $q = "UPDATE priv8users SET conf_link_clicked=CURRENT_TIMESTAMP, " .
             "is_registered=TRUE WHERE user_id=$user_id;";
        $mysqli->query($q);
    }
}

if ($accept) {
    $page_title = _("Registration complete");
    $body_text = sprintf(_("Your registration has been successful. You can now <a href=\"%s\">log in</a> using the credentials you provided"), $GLOBALS['WEBROOT'] . '/login');
}
else {
    $page_title = _("Registration unsuccessful.");
    $body_text = sprintf(_("Sorry, but your registration has not been successful. Please make sure you entered the confirmation URL correctly. If your confirmation email was sent more than one week ago, please return to the <a href=\"%s\">registration page</a> and start again."), $GLOBALS['WEBROOT'] . '/register');
}

$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>$page_title</p>
  </div>
  <div class="container">
	<p class="lead">$body_text</p>
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
