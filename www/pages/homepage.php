<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

$page_title = 'Home Page';
$page_content = <<<END_PAGE
<div class="jumbotron text-center">
  <h1><span class="fa fa-user-secret"></span>Priv8Chat</h1>
  <p>Secure online messaging</p>
</div>
<div class="container">
  <div class="row mb-1">
      <div class="col-sm-12">
      <p class="lead">This service allows you to exchange short messages with other users. Strong
          encryption is used to ensure that the messages you send can only be read by their
          intended recipients.</p>
      </div>
  </div>
  <div class="card-deck">
      <div class="card border-success text-center">
          <a href="#" role="button" class="btn-like">
          <div class="card-body">
          <p><span class="btn btn-success btn-lg">Sign In</span></p>
          <p>Have you already set up an account? Click here to sign in with your password.</p>
          </div>
          </a>
      </div>
      <div class="card border-primary text-center">
          <a href="/register" role="button" class="btn-like">
          <div class="card-body">
          <p><span class="btn btn-primary btn-lg">Sign Up</span></p>
          <p>Set up a new account. It only takes a minute, and it costs absolutely nothing.</p>
          </div>
          </a>
      </div>
  </div>
  <div class="row mt-4">
      <div class="col-sm-12">
      <h2>About this site</h2>
      <p>This is actually my submission for the
        <a href="https://www.coursera.org/learn/cyber-security-capstone/">Cybersecurity Capstone Project</a>
          run by the University of Maryland via Coursera. But you’re free to use it if you like. You’re also encouraged
          to test its security and look for vulnerabilities. But please don’t flood this server with traffic; I have
          to pay for the bandwidth.</p>
      </div>
  </div>
</div>
END_PAGE;

include $GLOBALS['APPROOT'] . '/views/main.php';
