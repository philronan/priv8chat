<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

$page_title = 'Cookie Statement';
$page_content = <<<END_PAGE
  <div class="jumbotron">
    <h1>🔒Priv8Chat</h1>
    <p>About Cookies on this Site</p>
  </div>
  <div class="container">
	<p class="lead">Cookies are very small text files that are stored on your
      computer when you visit some websites. This site use cookies to help identify
      your computer so you can send and receive messages without having to re-enter
      your user name and password at every step. You can delete or disable any cookies
      already stored on your computer, but doing so may stop this site from functioning
      properly.</p>
    <h3>Session cookie (name: <code>SecChat</code>)</h3>
    <p>This cookie consists of a string of random characters that uniquely identifies
      your web browser. Without it, you won't be able to send, receive or view any
      messages, or remain logged in to this site.</p>
    <h3>Third-party cookies</h3>
    <p>This website includes code hosted on other internet domains including googleapis.com
      (which hosts the web fonts used on this site), bootstrapcdn.com (used to deliver
      the Bootstrap front-end framework around which the design of this site was created),
      and jquery.com (the source of the JQuery Javascript library used to provide
      some of this site's interactive content). Deleting these so-called third-party
      cookies might cause minor temporary issues with the appearance and usability of this
      site.</p>
    <h3>Protecting your information</h3>
    <p>To create an account at this site, you will need to provide an email address. This
      information will not be shared with anyone else. For testing purposes, the site
      will — at least for a limited period — accept email addresses associated with anonymous
      accounts at <a href="https://www.guerrillamail.com/">guerrillamail.com</a>, which
      you are welcome to use instead of your regular email address.</p>
    <h3>Closing your account</h3>
    <p>After registering an account, you can delete your account at any time by
      following the instructions in your user profile page. Also, your account may
      be deleted automatically after six months or more of inactivity.</p>
  </div>

END_PAGE;

include "$ROOT/views/main.php";
