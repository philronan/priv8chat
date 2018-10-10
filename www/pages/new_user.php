<?php

function create_new_user($username, $password, $email) {
    $errors = [];

    // Make sure the input is valid. If not, return an array of error messages
    if (strlen($username) > 64) {
        array_push($errors, _("Your username cannot be longer than 64 characters"));
    }
    elseif (strlen($username) < 3) {
        array_push($errors, _("Your username cannot have fewer than 3 characters"));
    }
    if (!uses_valid_username_characters($username)) {
        array_push($errors, _("Your username contains one or more illegal characters"));
    }
    elseif (username_in_use($username)) {
        array_push($errors, _("That username is not available"));
    }
    if (!is_strong_password($password)) {
        array_push($errors, _("You need a stronger password than that"));
    }
    if (!valid_email_address_syntax($email)) {
        array_push($errors, _("Please provide a valid email address"));
    }
    else {
        // Ensure domain part of email address is in lower case
        $atpos = strpos($email, '@');
        $email = substr($email, 0, $atpos) . strtolower(substr($email, $atpos));
    }
    if (count($errors) > 0) return $errors;

    //
    $username_hash = get_username_hash($username);
    $username_enc = encrypt_username($username);
    $password_hash = get_password_hash($password);
    $email_hash = get_email_hash($email);
    $cookie_token = openssl_random_pseudo_bytes(16);


// Transform username to lower case before calculating the hash. This
// ensures that, e.g., john123 and John123 map to the same user. After
// base64 encoding, this results in a 44-character output.

function get_username_hash($username) {
    $username = strtolower($username) . $GLOBALS['USERNAME_HASH_SALT'];
    return openssl_digest($username, 'SHA256', true);
}


// Passwords need to be hashed more securely

function get_password_hash($password) {
    $salt = openssl_random_pseudo_bytes(16);
    $hash = openssl_pbkdf2($password,
                        $salt,
                        32,
                        10000,
                        'sha256');
    return base64_encode($salt . $hash);
}

// Emails are hashed verbatim

function get_email_hash($email) {
    return openssl_digest($email . $GLOBALS['EMAIL_HASH_SALT'], 'SHA256', true);
}



// Preserve capitalization in encrypted usernames. Pad to 64 characters
// to prevent breakers from guessing user names based on ciphertext length.
// Use randomized encryption to ensure that encrypted usernames leak no
// information.

function encrypt_username($username) {
    $username = str_pad($username, 64, '*');
    $iv = openssl_random_pseudo_bytes(16);
    $username_enc = openssl_encrypt($username,
                                'aes-128-cbc',
                                $GLOBALS['USERNAME_ENCRYPT_KEY'],
                                OPENSSL_RAW_DATA |  OPENSSL_ZERO_PADDING,
                                $iv);
    return base64_encode($iv . $username_enc);
}

// Reverse this process to decrypt usernames

function decrypt_username($username_enc) {
    $username_enc = base64_decode($username_enc);
    $iv = substr($username_enc, 0, 16);
    $ct = substr($username_enc, 16);
    $pt = openssl_decrypt($ct,
                        'aes-128-cbc',
                        $GLOBALS['USERNAME_ENCRYPT_KEY'],
                        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                        $iv);
    return rtrim($pt, '*');
}



/*
Key scheduling:

Master key = K (128 bits)
Username key = AES_128_ecb(K, "username::::::::")


Item                    Handling
------------------------------------------------------------------------
username_enc            Deterministic encryption with
