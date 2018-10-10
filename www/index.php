<?php

error_reporting(E_ALL);

// Force HTTPS connection
if(0 && $_SERVER['HTTPS'] != 'on')
{
    header('HTTP/1.1 302 Found');
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

// A flag to tell included files that they have been accessed via a
// request processed by this file.
$VIA_INDEX = TRUE;

$ROOT = $_SERVER['DOCUMENT_ROOT'];

// If gettext isn't installed, just use default strings
if (!function_exists('_')) {
    function _($s) { return $s; }
}


ob_start();

// Load config file, if it exists
$path = empty($_SERVER['PATH_INFO']) ? '' : trim($_SERVER['PATH_INFO'], '/');
if (file_exists("$ROOT/.config")) {
    $glob = parse_ini_file("$ROOT/.config");
    foreach ($glob as $key => $val) {
        $GLOBALS[$key] = $val;
    }

    // Database and session setup
    include("$ROOT/db/connect.php");

    switch ($path) {
        case '':
        case 'index.html':
            $path = 'homepage';
            include "$ROOT/pages/homepage.php";
            break;
        case 'register':
            include "$ROOT/pages/register.php";
            break;
        case 'login':
            include "$ROOT/pages/login.php";
            break;
        case 'inbox':
            include "$ROOT/pages/inbox.php";
            break;
        case 'write':
            include "$ROOT/pages/write.php";
            break;
        case 'cookie-info':
            include "$ROOT/pages/cookie-info.php";
            break;
        default:
            include "$ROOT/pages/404.php";
    }
}

// If there is no config file, then we have some setting-up to do
else {
    $path = 'setup';
    include("$ROOT/pages/setup.php");
}
ob_end_flush();
