<?php

$APPROOT = dirname(dirname(__FILE__)); // **TODO** Fix ugly code
include("$APPROOT/db/connect.php");    // (should indlude /index.php instead)
include("$APPROOT/crypto.php");

$username = @$_GET['n'];

sleep(2);
$data = [ "name" => $username, "valid" => true, "comment" => "Looks OK :-)" ];
if (preg_match('/^[A-Za-z0-9_]{1,64}$/', $username) != 1) {
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

