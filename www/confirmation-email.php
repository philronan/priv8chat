<?php

define('TESTING', false);

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

define('FROM_ADDRESS', 'robot@' . $_SERVER['HTTP_HOST']);


function signup_email($username, $email, $token) {
    $WEBROOT = dirname($_SERVER['SCRIPT_NAME']);
    if ($WEBROOT == '/') $WEBROOT = '';
    $subject = _("Priv8chat Signup");
    $site_link = 'https://' . $_SERVER['HTTP_HOST'] . '/';
    $conf_link = 'https://' . $_SERVER['HTTP_HOST'] . "$WEBROOT/confirm?name=$username&token=$token";
    $conf_link_escaped = str_replace('&', '&amp;', $conf_link);
    $body1 = sprintf(_("Dear %s, thank you for signing up to Priv8chat (%s). To complete your registration, please click the following link:"), $username, $site_link);
    $body2 = sprintf(_("Note: If you didn't sign up for this service, then it appears that someone with the IP address %s has mistakenly filled in your e-mail address on our website. Sorry about that. But don't worry; you haven't been added to any mailing lists, and you won't hear from us again if you don't click the confirmation link."), $_SERVER['REMOTE_ADDR']);
    $bodyhtml = "<p>$body1</p>\n<p><a href=\"$conf_link_escaped\">$conf_link_escaped</a></p>\n<p>$body2</p>\n";
    $delim = 'priv8chat-' . randomPassword(24);
    $mail_content = <<<END_MAIL
--$delim
Content-Transfer-Encoding: 7bit
Content-Type: text/plain;
\tcharset=us-ascii

PRIV8CHAT
=========

$body1

$conf_link

$body2


--$delim
Content-Transfer-Encoding: 7bit
Content-Type: text/html;
\tcharset=us-ascii

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
</head>
<body style="font-size: 1.25rem; word-wrap: break-word; overflow-wrap: break-word;">
    <h1 style="font-family: monospace">PRIV8CHAT</h1>
    <p>$body1</p>
    <p><a href="$conf_link_escaped">$conf_link_escaped</a></p>
    <p><small>$body2</small></p>
</body>
</html>

--$delim--

END_MAIL;

    $xtra_headers = "From: " . FROM_ADDRESS . "\n" .
                    "Content-Type: multipart/alternative;\n\tboundary=\"$delim\"\n" .
                    "Mime-Version: 1.0";
    if (TESTING) {
        header("Content-Type: text/plain");
        echo <<<END_CONF
To: $email
Subject: $subject
$xtra_headers

$mail_content
END_CONF;
        die();
    }
    else {
        return mail($email, $subject, $mail_content, $xtra_headers);
    }
}
