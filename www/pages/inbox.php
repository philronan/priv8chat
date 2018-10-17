<?php

if (!@$VIA_INDEX) die(''); // No output unless accessed via /index.php

// include $GLOBALS['APPROOT'] . '/crypto.php';


// Invite the user to log out if already logged in
if (!$_SESSION['logged_in']) {
    $page_content = <<<END_PAGE
<div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Inbox</p>
</div>
<div class="container">
    <div class="col-sm-12">
    <p class="lead">Hi, you're going to have to <a href="login">log in</a> before you can see your messages.</p>
</div>

END_PAGE;
    include $GLOBALS['APPROOT'] . '/views/main.php';
    die();
}

// Fetch the messages adddressed to this user
$user_id = $_SESSION['user_id'];
$mailtable = "";
$q = "SELECT * FROM priv8messages WHERE to_user=$user_id AND receiver_deleted=FALSE ORDER BY when_sent DESC;";
$res = $mysqli->query($q);
if ($res->num_rows == 0) {
    $comment = _("Nothing to display here. Your inbox is empty.");
}
else {
    $comment = sprintf(_("You have %d message(s) in your inbox"), $res->num_rows);
    while ($row = $res->fetch_assoc()) {
        $message_id = $row['message_id'];
        $from_user = $row['from_user'];
        $r = $mysqli->query("SELECT username_encrypted FROM priv8users WHERE user_id=$from_user;");
        $t = $r->fetch_assoc();
        $from = username_decrypt($t['username_encrypted']);
        $when_sent = date("Y-m-d", strtotime($row['when_sent']));
        $subject = htmlspecialchars(text_decrypt($row['message_subject']));
        $unread = intval($row['has_been_read']) ? "" : " unread";
        $mailtable .= <<<END_MAIL_ITEM
        <a href="/read?m=$message_id"><div class="row pt-1 pb-1$unread">
          <div class="col-sm-4"><span class="date">$when_sent</span> <span class="sender">$from</span></div>
          <div class="col-sm-8">$subject</div>
        </div></a>

END_MAIL_ITEM;
    }
}


$action = $GLOBALS['WEBROOT'] . '/login';


$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Inbox</p>
  </div>
  <div class="container mailbox">
    <div class="row">
      <div class="col-sm-12">
        <p>$comment</p>
      </div>
    </div>
$mailtable
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
