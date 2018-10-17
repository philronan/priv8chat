<?php

if (!@$VIA_INDEX) die(''); // No output unless accessed via /index.php

// include $GLOBALS['APPROOT'] . '/crypto.php';

// Invite the user to log out if already logged in
if (!$_SESSION['logged_in']) {
    $page_content = <<<END_PAGE
<div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Write mail</p>
</div>
<div class="container">
    <div class="col-sm-12">
    <p class="lead">Hi, you're going to have to <a href="login">log in</a> before you can do that.</p>
</div>

END_PAGE;
    include $GLOBALS['APPROOT'] . '/views/main.php';
    die();
}

$page_title = 'Write mail';

$action = $GLOBALS['WEBROOT'] . '/write';

$to = "";
$subject = "";
$body = "";
$errors = [];

while ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limit output to 10 emails per user per hour
    $uid = $_SESSION['user_id'];
    $anhourago = time() - 3600;
    $q = "SELECT COUNT(*) AS total FROM priv8messages WHERE from_user=$uid AND when_sent>$anhourago;";
    $r = $mysqli->query($q);
    $d = $r->fetch_assoc();
    if ($d['total'] >= 10) {
        $page_content = <<<END_THROTTLE_ERROR
<div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Write mail</p>
</div>
<div class="container">
    <div class="col-sm-12">
    <p class="lead">Sorry, but users are currently limited to sending 10 mails per hour. Try again later.</p>
</div>

END_THROTTLE_ERROR;
        include $GLOBALS['APPROOT'] . '/views/main.php';
        die();
    }

    // fetch form input and validate
    $to = trim(@$_POST['username']);
    $subject = trim(@$_POST['subject']);
    $body = @$_POST['message'];

    if (strlen($subject) > 300) {
        $errors[] = "The subject line only need to be a few words. Please make it shorter.";
    }
    if (strlen($subject) < 1) {
        $errors[] = "You can't leave the subject line empty.";
    }
    if (strlen($body) > 100000) {
        $errors[] = "That message is way too long. Please make it shorter.";
    }
    if (count($errors)) break;

    // Does the recipient actually exist?
    $e = username_encrypt($to);
    $q = "SELECT user_id FROM priv8users WHERE username_encrypted='$e';";
    $r = $mysqli->query($q);
    if ($r->num_rows == 0) {
        // Pause here in case the user is trying to discover user names
        // by brute force
        sleep(1);
        $errors[] = "The recipient user doesn't exist. Please check your typing.";
        break;
    }
    $t = $r->fetch_assoc();
    $recipient_id = $t['user_id'];
    $sub = text_encrypt($subject);
    $bod = text_encrypt($body);

    // Everything seems OK. Let's go ahead and store this message.
    $q = "INSERT INTO priv8messages (from_user, to_user, message_subject, message_text) VALUES " .
         "($uid, $recipient_id, '$sub', '$bod')";
    if ($mysqli->query($q) == FALSE) {
        die("Failed while writing to database");
    }

    // Success!
    $new_loc = 'https://' . $_SERVER['HTTP_HOST'] . $GLOBALS['WEBROOT'] . '/sent';
    header("HTTP/1.1 303 See other");
    header("Location: $new_loc");
    die("If you had a decent browser, you wouldn't have to <a href=\"$new_loc\">click here to continue</a>.");
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

$to = htmlspecialchars($to);
$subject = htmlspecialchars($subject);
$body = htmlspecialchars($body);

$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Write mail</p>
  </div>
  <div class="container">
    <div class="col-sm-12">
$error_report
      <p>Enter your message below.</p>
    </div>
    <div class="col-sm-12">
    <div class="well">
        <form id="emailform" class="regform" action="$action" method="POST">
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="username">To:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="$to"
                               id="username" name="username" maxlength="64"
                               pattern="[A-Za-z0-9_]{1,64}" tabindex="1" required autofocus>
                    </div>
                </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="subject">Subject:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="$subject"
                               id="subject" name="subject" maxlength="300"
                               tabindex="2" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12">
                    <label for="body">Message:</label>
                    <div class="input-group">
                        <textarea cols="20" rows="10" width="100%" class="form-control"
                               id="message" name="message" maxlength="100000"
                               tabindex="3">$body</textarea>
                    </div>
                </div>
            </div>
            <div class="form-row mt-3">
                <div class="col-sm-12 text-center">
                <button class="btn btn-primary" type="submit">Send</button>
            </div>
        </form>
      </div>
    </div>
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
