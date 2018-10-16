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

$GLOBALS['DOCROOT'] = $_SERVER['DOCUMENT_ROOT'];
$GLOBALS['WEBROOT'] = (dirname($_SERVER['SCRIPT_NAME']) == '/') ? '' : dirname($_SERVER['SCRIPT_NAME']);
$GLOBALS['APPROOT'] = dirname(__FILE__);

// If gettext isn't installed, just use default strings
if (!function_exists('_')) {
    function _($s) { return $s; }
}

// Session parameters
session_start([
    'save_path' => $GLOBALS['APPROOT'] . '/.sessions',
    'name' => 'PRIV8COOKIE',
    'use_strict_mode' => TRUE,
    'use_cookies' => TRUE,
    'use_only_cookies' => TRUE,
    'cookie_lifetime' => 86400,
    'cookie_path' => '/',
//    'cookie_secure' => TRUE,
    'cookie_httponly' => TRUE
]);

ob_start();

// Load config file, if it exists
$path = empty($_SERVER['PATH_INFO']) ? '' : trim($_SERVER['PATH_INFO'], '/');
$config_file = $GLOBALS['APPROOT'] . '/.config';

if (file_exists($config_file)) {
    $glob = parse_ini_file($config_file);
    foreach ($glob as $key => $val) {
        $GLOBALS[$key] = $val;
    }

    // Database and session setup
    include($GLOBALS['APPROOT'] .'/db/connect.php');

    switch ($path) {
        case '':
        case 'index.html':
            $path = 'homepage';
            include $GLOBALS['APPROOT'] . '/pages/homepage.php';
            break;
        case 'register':
            include $GLOBALS['APPROOT'] . '/pages/register.php';
            break;
        case 'confirm':
            include $GLOBALS['APPROOT'] . '/pages/confirm.php';
            break;
        case 'welcome':
            include $GLOBALS['APPROOT'] . '/pages/welcome.php';
            break;
        case 'login':
            include $GLOBALS['APPROOT'] . '/pages/login.php';
            break;
        case 'inbox':
            include $GLOBALS['APPROOT'] . '/pages/inbox.php';
            break;
        case 'write':
            include $GLOBALS['APPROOT'] . '/pages/write.php';
            break;
        case 'cookies':
            include $GLOBALS['APPROOT'] . '/pages/cookie-info.php';
            break;
        default:
            include $GLOBALS['APPROOT'] . '/pages/404.php';
    }
}

// If there is no config file, then we have some setting-up to do
else {
    $path = 'setup';
    include($GLOBALS['APPROOT'] . '/pages/setup.php');
}
ob_end_flush();
