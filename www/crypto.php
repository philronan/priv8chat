<?php

# Encrypt/decrypt user names. Use a deterministic method so that we can
# search the database for a user name without having to decrypt them all.
# The loss of semantic security isn't a problem because every user name
# should be unique, and by using the MD5 hash of the username as an IV, we
# can ensure that similar inputs give dissimilar outputs.

function username_encrypt($username) {
    // Username is not case sensitive, so force lowercase. Also strip spaces
    $username = strtolower(trim($username));
    if (strlen($username) > 64) die("wtf");
    // Pad to 64 characters with spaces
    $username .= str_repeat(' ', 64-strlen($username));
    $hash = md5($GLOBALS['SALT'] . ":$username", 1);
    $ct = openssl_encrypt($username, 'AES-128-CBC', $GLOBALS['USERNAME_KEY'],
                          OPENSSL_ZERO_PADDING | OPENSSL_RAW_DATA, $hash);
    // Returns a string of 108 characters
    return base64_encode($hash . $ct);
}

function username_decrypt($ciphertext) {
    $raw = base64_decode($ciphertext);
    $hash = substr($raw, 0, 16);
    $ct = substr($raw, 16);
    $pt = openssl_decrypt($ct, 'AES-128-CBC', $GLOBALS['USERNAME_KEY'],
                          OPENSSL_ZERO_PADDING | OPENSSL_RAW_DATA, $hash);
    if (md5($GLOBALS['SALT'] . ":$pt", 1) !== $hash) die("Wtf");
    return rtrim($pt);
}


# The database doesn't store email addresses. Just their hashes.

function email_hash($email) {
    // Returns a string of 44 characters
    return base64_encode(openssl_digest($email, 'SHA256', 1));
}


// Randomized encryption functions for messages and subject lines
// Use compression to save disk space. Let OpenSSL take care of padding.

function text_encrypt($txt) {
    $iv = openssl_random_pseudo_bytes(16);
    $pt = gzdeflate($txt);
    $ct = $iv . openssl_encrypt($pt, 'AES-128-CBC', $GLOBALS['MESSAGE_KEY'],
                                OPENSSL_RAW_DATA, $iv);
    return base64_encode($ct);
}

function text_decrypt($ciphertext) {
    $raw = base64_decode($ciphertext);
    $iv = substr($raw, 0, 16);
    $ct = substr($raw, 16);
    $pt = openssl_decrypt($ct, 'AES-128-CBC', $GLOBALS['MESSAGE_KEY'],
                          OPENSSL_RAW_DATA, $iv);
    return gzinflate($pt);
}


// Password hashing and verification. These are based on PHP's built-in
// functions, but with a preliminary sha256 hash applied to the password
// in order to escape bcrypt's 72-character password limit. The output of
// sc_password_hash is a 60-character string. 2^10 key expaneion rounds
// are recommended, but we're using strong passwords here so I think it's
// safe to drop down to 2^8 and reduce the server load a little.

function sc_password_hash($password) {
    $hash = base64_encode(openssl_digest($password, 'SHA256', 1));
    return password_hash($hash, PASSWORD_BCRYPT, ['cost'=>8]);
}

function sc_password_verify($password, $hash) {
    $chkhash = base64_encode(openssl_digest($password, 'SHA256', 1));
    return password_verify($chkhash, $hash);
}


// User IDs are sequential numbers, but to make it hard for one user to
// learn the identity of another, the values are encrypted. **TODO**

function user_id_encrypt($user_id) {
    return base64_encode(openssl_encrypt(printf('%016x',$user_id), 'aes-128-ecb',
        $GLOBALS['USER_ID_KEY'], OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING));
}

function user_id_decrypt($user_token) {
    return intval(openssl_decrypt(base64_decode($user_token), 'aes-128-ecb',
        $GLOBALS['USER_ID_KEY'], OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING), 16);
}
