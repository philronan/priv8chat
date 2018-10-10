<!DOCTYPE html>
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
    <script src="js/zxcvbn.js"<?php if ($path != 'register') echo ' defer'; ?>></script>
    <link href="/css/main.css" rel="stylesheet">
    <title>Priv8Chat :: <?php echo $page_title; ?></title>
</head>
<body class="<?php echo $path; ?>">
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
<script src="/js/eurocookie.js"></script>
<script src="/js/randomPassword.js"></script>
<script src="/js/form-validation.js"></script>
</body>
</html>
