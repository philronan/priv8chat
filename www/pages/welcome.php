<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

$page_title = 'Cookie Statement';
$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>Welcome</p>
  </div>
  <div class="container">
        <p class="lead">A confirmation link has been sent to your email address.
          Please click this link to complete your registration.</p>
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
