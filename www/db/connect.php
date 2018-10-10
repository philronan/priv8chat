<?php
/*
$SECRET_KEYS = parse_ini_file("$ROOT/._keys.cfg");

$mysqli = new mysqli("localhost", "sec_chat_admin", $SECRET_KEYS["MYSQL_PASSWORD"], "sec_chat");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

ini_set("session.name", "SecChat");
ini_set("session.cookie_lifetime", 3600 * 24 * 60);
ini_set("session.use_only_cookies", true);
ini_set("session.cookie_httponly", true);
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    ini_set("session.cookie_secure", true);
}

session_start();
if (!isset($_SESSION['tick_count'])) {
    $_SESSION['tick_count'] = 1;
    $_SESSION['start_time'] = time();
    $_SESSION['last_active'] = time();
}
else {
    $_SESSION['tick_count'] += 1;
    $_SESSION['last_active'] = time();
}
*/
