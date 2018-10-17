<?php

if (!@$VIA_INDEX) die(''); // No output unless accessed via /index.php

// include $GLOBALS['APPROOT'] . '/crypto.php';

// Invite the user to log out if already logged in
if (!$_SESSION['logged_in']) {
    $page_content = <<<END_PAGE
<div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Read mail</p>
</div>
<div class="container">
    <div class="col-sm-12">
    <p class="lead">Hi, you're going to have to <a href="login">log in</a> before you can do that.</p>
</div>

END_PAGE;
    include $GLOBALS['APPROOT'] . '/views/main.php';
    die();
}

$page_title = 'Read mail';
$uid = $_SESSION['user_id'];
$action = $GLOBALS['WEBROOT'] . '/read';

$errors = [];

while ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $m = intval($_POST['delete']);
        $q = "SELECT * FROM priv8messages WHERE message_id=$m AND (from_user=$uid OR to_user=$uid);";
        $r = $mysqli->query($q);
        if ($r->num_rows < 1) {
            $msg_html = "<p>That message doesn't exist.</p>";
            break;
        }
        $row = $r->fetch_assoc();
        $sd = intval($row['sender_deleted']);
        $rd = intval($row['receiver_deleted']);
        if (intval($row['from_user']) == $uid) $sd=1;
        if (intval($row['to_user']) == $uid) $rd=1;
        if ($sd && $rd) {
            $mysqli->query("DELETE FROM priv8messages WHERE message_id=$m");
        }
        else {
            $mysqli->query("UPDATE priv8messages SET sender_deleted=$sd,receiver_deleted=$rd WHERE message_id=$m");
        }

        // go back to inbox
        $new_loc = 'https://' . $_SERVER['HTTP_HOST'] . $GLOBALS['WEBROOT'] . '/inbox';
        header("HTTP/1.1 303 See other");
        header("Location: $new_loc");
        die("If you had a decent browser, you wouldn't have to <a href=\"$new_loc\">click here to continue</a>.");
    }

}

// Very important:
// Don't show the message unless it was written by or addressed to the current user!

$msg_html = '';
$m = intval(@$_GET['m']);
$q = "SELECT * FROM priv8messages WHERE message_id=$m AND (from_user=$uid OR to_user=$uid);";
$r = $mysqli->query($q);
if ($r->num_rows < 1) {
    $msg_html = "<p>That message doesn't exist.</p>";
}
else {
    $row = $r->fetch_assoc();
    $date = date("Y-m-d H:i:s", strtotime(intval($row['when_sent'])));
    $subj = htmlspecialchars(text_decrypt($row['message_subject']));
    $body = htmlspecialchars(text_decrypt($row['message_text']));
    $subj = preg_replace('/[\r\n]+/', " ", $subj);
    $body = preg_replace('/(\r\n?)+/', "</p><p>", $body);
    $fromid = intval($row['from_user']);
    $toid = intval($row['to_user']);
    $q = "SELECT username_encrypted FROM priv8users WHERE user_id=$fromid;";
    $r = $mysqli->query($q);
    if ($r->num_rows == 0) {
        // strange...?
        $from = "[Unknown user]";
    }
    else {
        $t = $r->fetch_assoc();
        $from = htmlspecialchars(username_decrypt($t['username_encrypted']));
    }
    $q = "SELECT username_encrypted FROM priv8users WHERE user_id=$toid;";
    $r = $mysqli->query($q);
    if ($r->num_rows == 0) {
        // strange...?
        $from = "[Unknown user]";
    }
    else {
        $t = $r->fetch_assoc();
        $to = htmlspecialchars(username_decrypt($t['username_encrypted']));
    }

    $q = "UPDATE priv8messages SET has_been_read=1 WHERE message_id=$m";
    $mysqli->query($q);

    $msg_html = "<div style=\"font-size:80%\">\n<p>$body</p>\n</div>";
}
$root = $GLOBALS['WEBROOT'];
$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Read mail</p>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <form method="POST" class="pull-right" action="$root/read" style="display:inline"><button class="nav-link post-link" type="submit" name="delete" value="$m"><small><span class="fa fa-trash"></span> Delete this message</small></form>
      </div>
    </div>
    <div class="row unread pt-1 pb-1 mb-2">
      <div class="col-sm-12">
        Date: $date<br>From: $from<br>To: $to<br>Subject: <strong>$subj</strong>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
$msg_html
      </div>
    </div>
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
