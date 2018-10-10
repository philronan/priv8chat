<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];
include("$ROOT/db/connect.php");
include("$ROOT/crypto/primitives.php");

$username = isset($_GET['n']) ? $_GET['n'] : '';
$nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';
if ($nonce != $_SESSION['register_nonce']) die ("Authentication error");

sleep(2);
$data = [ "name" => $username, "valid" => true, "comment" => "Looks OK :-)" ];
if (preg_match('/^[A-Za-z0-9_]{6,64}$/', $username) != 1) {
    $data['valid'] = false;
    $data['comment'] = "Invalid format.";
}
else {
    $e = encrypt_username($username);
    $res = $mysqli->query("SELECT * FROM users WHERE username_encrypted='$e';");
    if ($res->num_rows > 0) {
        $data['valid'] = false;
        $data['comment'] = "Sorry, someone else is already using that name. Try adding a few more characters.";
    }
}
$result = json_encode($data) . "\n";
header("Content-Type: application/json; charset=utf-8");
header("Content-Length: " . strlen($result));
echo $result;
