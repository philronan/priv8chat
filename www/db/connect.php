<?php

// Key scheduling, etc.

// Fetch values of DBNAME, DBUSER, DBPASS and MASTERKEY
$SECRET_KEYS = parse_ini_file($GLOBALS['APPROOT'] . '/.config');

$GLOBALS['DBNAME'] = $SECRET_KEYS['DBNAME'];
$GLOBALS['DBUSER'] = $SECRET_KEYS['DBUSER'];
$GLOBALS['DBPASS'] = base64_decode($SECRET_KEYS['DBPASS']);

// Use the master key to generate other keys/salt values
$key = hex2bin($SECRET_KEYS['MASTERKEY']);
$username_key = openssl_encrypt('usernameusername', 'aes-128-ecb', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
$message_key  = openssl_encrypt('messagemessageme', 'aes-128-ecb', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
$nonce_key    = openssl_encrypt('noncenoncenoncen', 'aes-128-ecb', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
$salt         = openssl_encrypt('saltsaltsaltsalt', 'aes-128-ecb', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
$user_id_key  = openssl_encrypt('useriduseriduser', 'aes-128-ecb', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

$GLOBALS['USERNAME_KEY'] = $username_key;
$GLOBALS['MESSAGE_KEY'] = $message_key;
$GLOBALS['NONCE_KEY'] = $nonce_key;
$GLOBALS['USER_ID_KEY'] = $user_id_key;
// convert this one to ASCII because it is concatenated to the email address
$GLOBALS['SALT'] = base64_encode($salt);

// Remove the master key from memory. Not sure this is necessary, but anyway...
unset($key, $SECRET_KEYS);



// Connect to database

$mysqli = new mysqli('localhost', $GLOBALS['DBUSER'], $GLOBALS['DBPASS'], $GLOBALS['DBNAME']);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: ' . $mysqli->connect_error);
}

ini_set('session.name', 'SecChat');
ini_set('session.cookie_lifetime', 3600 * 24 * 60);
ini_set('session.use_only_cookies', true);
ini_set('session.cookie_httponly', true);
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    ini_set('session.cookie_secure', true);
}

if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['tick_count'])) {
    $_SESSION['tick_count'] = 1;
    $_SESSION['start_time'] = time();
    $_SESSION['last_active'] = time();
}
else {
    $_SESSION['tick_count'] += 1;
    $_SESSION['last_active'] = time();
}

