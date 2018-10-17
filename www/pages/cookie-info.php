<?php

if (!@$VIA_INDEX) die(''); // No output unless accessed via /index.php

$page_title = 'Cookie Statement';
$page_content = <<<END_PAGE
  <div class="jumbotron text-center">
    <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
    <p>About Cookies on this Site</p>
  </div>
  <div class="container">
	<p class="lead">Cookies are very small text files that are stored on your
      computer when you visit some websites. This site use cookies to help identify
      your computer so you can send and receive messages without having to re-enter
      your user name and password at every step. You can delete or disable any cookies
      already stored on your computer, but doing so may stop this site from functioning
      properly.</p>
    <h3>Session cookie (name: <code>PRIV8COOKIE</code>)</h3>
    <p>This cookie consists of a string of random characters that uniquely identifies
      your web browser. Without it, you won't be able to send, receive or view any
      messages, or remain logged in to this site. </p>
    <h3>EU cookie (name: <code>eu_cookie</code>)</h3>
    <p>EU law requires that you are given the option to refuse cookies from this site.
      This cookie is set when you have acknowledged this fact.</p>
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
      information will not be shared with anyone else. In fact, it <em>can't</em> be shared
      at all, because once your confirmation email has been sent, the server applies a
      one-way hash function to your email address. This function is impossible to reverse,
      except by trying all possible email addresses one at a time. For testing purposes, this
      site will — at least for a limited period — accept email addresses associated with
      anonymous accounts at <a href="http://www.sharklasers.com/">sharklasers.com</a>, which
      you are welcome to use instead of your regular email address.</p>
    <h3>Deleting your account</h3>
    <p>This is a feature I meant to add, but didn't have time. For the time being, the plan
      is to reset the entire database on completion of the Cybersecurity Capstone project
      (in a few weeks' time).</p>
  </div>

END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
