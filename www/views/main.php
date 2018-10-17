<?php

$base = $GLOBALS['WEBROOT'];

?><!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,700|Share+Tech+Mono&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">

    <!-- Preload password strengthometer (needed on registration page) -->
    <script src="<?php echo $base; ?>/js/zxcvbn.js"<?php if ($path != 'register') echo ' defer'; ?>></script>
    <script src="<?php echo $base; ?>/js/copytext.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous" rel="stylesheet">
    <link href="<?php echo $base; ?>/css/main.css" rel="stylesheet">
    <!-- ****** faviconit.com favicons ****** -->
	<link rel="shortcut icon" href="<?php echo $base; ?>/faviconit/favicon.ico">
	<link rel="icon" sizes="16x16 32x32 64x64" href="<?php echo $base; ?>/faviconit/favicon.ico">
	<link rel="icon" type="image/png" sizes="196x196" href="<?php echo $base; ?>/faviconit/favicon-192.png">
	<link rel="icon" type="image/png" sizes="160x160" href="<?php echo $base; ?>/faviconit/favicon-160.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $base; ?>/faviconit/favicon-96.png">
	<link rel="icon" type="image/png" sizes="64x64" href="<?php echo $base; ?>/faviconit/favicon-64.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base; ?>/faviconit/favicon-32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $base; ?>/faviconit/favicon-16.png">
	<link rel="apple-touch-icon" href="<?php echo $base; ?>/faviconit/favicon-57.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $base; ?>/faviconit/favicon-114.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $base; ?>/faviconit/favicon-72.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $base; ?>/faviconit/favicon-144.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $base; ?>/faviconit/favicon-60.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $base; ?>/faviconit/favicon-120.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $base; ?>/faviconit/favicon-76.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $base; ?>/faviconit/favicon-152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $base; ?>/faviconit/favicon-180.png">
	<meta name="msapplication-TileColor" content="#FFFFFF">
	<meta name="msapplication-TileImage" content="<?php echo $base; ?>/faviconit/favicon-144.png">
	<meta name="msapplication-config" content="<?php echo $base; ?>/faviconit/browserconfig.xml">
	<!-- ****** faviconit.com favicons ****** -->
    <title>Priv8Chat :: <?php echo $page_title; ?></title>
</head>
<body class="<?php echo $path; ?>">
    <nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
     <!-- Brand -->
     <a class="navbar-brand" href="<?php echo $base; ?>/"><span class="fa fa-user-secret"></span>Priv8Chat</a>

     <!-- Toggler/collapsibe Button -->
     <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
       <span class="navbar-toggler-icon"></span>
     </button>

     <!-- Navbar links -->
     <div class="collapse navbar-collapse" id="collapsibleNavbar">
       <ul class="navbar-nav">
<?php if ($_SESSION['logged_in']): ?>
         <li class="nav-item">
           <a class="nav-link" href="<?php echo $base; ?>/inbox"><span class="fa fa-inbox"></span> Inbox</a>
         </li>
         <li class="nav-item">
           <a class="nav-link" href="<?php echo $base; ?>/sent"><span class="fa fa-paper-plane"></span> Sent mail</a>
         </li>
         <li class="nav-item">
           <a class="nav-link" href="<?php echo $base; ?>/write"><span class="fa fa-pencil"></span> Write mail</a>
         </li>
<?php else: ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo $base; ?>/register"><span class="fa fa-user-plus"></span> Register</a>
        </li>
<?php endif; ?>
         <li class="nav-item">
           <a class="nav-link" href="<?php echo $base; ?>/dbdump"><span class="fa fa-download"></span> DB dump</a>
         </li>
       </ul>
       <ul class="navbar-nav ml-auto">
         <li class="nav-item">
<?php if ($_SESSION['logged_in']): ?>
             <form method="POST" action="<?php echo $base; ?>/logout" style="display:inline"><button class="nav-link post-link" type="submit" title="Currently logged in as <?php echo htmlspecialchars($_SESSION['username']); ?>"><span class="fa fa-sign-out"></span> Log out</button></form>
<?php else: ?>
             <a class="nav-link" href="<?php echo $base; ?>/login"><span class="fa fa-sign-in"></span> Log in</a>
<?php endif; ?>
         </li>
       </ul>
     </div>
   </nav>
<noscript>
    <div id="javascript_inactive_warning_container" class="container">
        <div class="fixed-top bg-warning">
            <div class="container mb-2 mt-2">
                <div class="row">
                    <div class="col-sm-12"><strong>Warning:</strong> This site won't work at all without Javascript.
                        Please enable Javascript in your browser, or if this is not possible, please switch to a browser that supports it.</div>
                </div>
            </div>
        </div>
    </div>
</noscript>
<div class="container">
<?php echo $page_content; ?>
</div>
<?php
if ($path != 'setup' && (!isset($_COOKIE['eu_cookie']) || $_COOKIE['eu_cookie'] != 'accept')): ?>
<!-- Euro cookie alert -->
<div id="cookie_directive_container" class="container" style="display: none">
    <div class="fixed-bottom border-top border-secondary">
        <div class="container mb-2 mt-2"><div class="row">
        <div class="col-sm-10"><small>By using this website you are consenting to the use of cookies in accordance with the <a href="/cookies">cookie policy</a>.</small></div>
        <div class="col-sm-2" id="cookie_accept"><a href="javascript:void(0)" class="btn-like btn btn-sm btn-success"><small>OK, got it</small></a></div>
        </div></div>
    </div>
</div>
<?php endif; ?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<script src="<?php echo $base; ?>/js/eurocookie.js"></script>
<script src="<?php echo $base; ?>/js/randomPassword.js"></script>
<script src="<?php echo $base; ?>/js/form-validation.js"></script>
</body>
</html>
