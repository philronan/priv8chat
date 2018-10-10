<?php

require("zxcvbn-loader.php");

function randomPassword($numChars) {
    $alphabet = "256789bcdfghjkmnpqrstvwxz";
    $pw = "";
    for ($i=0; $i<$numChars; $i++) {
        do {
            $rnd = ord(openssl_random_pseudo_bytes(1)) & 0x1f;
        } while ($rnd >= strlen($alphabet));
        $pw .= substr($alphabet, $rnd, 1);
        // Add hyphen after every 4 chars if numChars is a multiple of 4
        if ($numChars%4 == 0 && $i%4 == 3 && $i<$numChars-1) $pw .= '-';
    }
    return $pw;
}

function check_config() {
    $here = getcwd();
    $conf_file = "$here/.scryptorium_config";
    if (file_exists($conf_file)) {
        // If a config file already exists, it is probably from a previous
        // installation. Does it look OK?
        $config = parse_ini_file($conf_file);
        if ($config === FALSE                   ||
            !isset($config['MYSQL_USER'])       ||
            !isset($config['MYSQL_DB_NAME'])    ||
            !isset($config['MYSQL_PASSWORD'])   ||
            !isset($config['MASTER_KEY'])       ||
            !is_strong_password($config['MYSQL_PASSWORD']) ||
            !is_strong_password($config['MASTER_KEY'])
        ) {
            $reply = sprintf(_("A config file exists at %s, but appears to be" .
                               " unusable. Perhaps you should delete it and" .
                               " try again?", $config_file));
            return $reply;
        }
        else {
            // We have a config file, and it looks OK
            return "";
        }
    }
    // There is no config file at present, so let's try to create one now
    if (!is_writable($here)) {
        $reply = sprintf(_("Unable to create a config file in the %s" .
                           " directory. Please make sure that PHP has" .
                           " permission to write to this location", $here));
        return $reply;
    }
}


// is_writable
// file_exists

$pw = randomPassword(20);

$userInfo = posix_getpwuid(posix_getuid());
$user = $userInfo['name'];
$groupInfo = posix_getgrgid(posix_getgid());
$group = $groupInfo = $groupInfo['name'];


?><html>
<head>
<style>
body { background-color: #aaa; }
pre { background-color: #ccc; padding: 0.5rem 1rem; white-space: pre-wrap; }
code { background-color: #ccc; padding: 0.1em 0.2em; }
#content { max-width: 40rem; margin:3rem auto; }
</style>
</head>
<body>
    <div id="content">
        <h1>Installation</h1>
        <p>Scryptorium is easy to install. Just follow these steps:</p>
        <ol>
            <li>Unpack the source files in the directory of your webserver from where you
            want this software to run. (If you're reading this in your web browser, then
            you've probably done this step already.)</li>
            <li>Use <code>chown</code> to assign ownership of this directory and everything
            it contains to the webserver software. You can do this by opening a command line
            console and pasting in the following commands:
            <pre>sudo chown -R <?php echo "$user:$group " . getcwd(); ?></pre></li>
            <li>Log into MySQL as root, and run the following instructions to create a
            database. (You can replace <code><?php echo $pw; ?></code> with a secure password
            of your own choosing, or generate another one by reloading this page.)
<pre>CREATE DATABASE `scryptorium`
    DEFAULT CHARACTER SET 'utf8';
GRANT ALL ON `scryptorium`.*
    TO `scryptadmin`@localhost
    IDENTIFIED BY '<?php echo $pw; ?>';
FLUSH PRIVILEGES;</pre></li>
            <li>Enter this password into the form below and click <em>Continue</em>.</li>
        </ol>
    </div>
</body>
</html>
<!--
$ini = parse_ini_file("./.secret");
foreach ($ini as $key => $val) {
    $GLOBALS["$key"] = $val;
}

$GLOBALS["USERNAME_HASH_SALT"] = '%' .
$GLOBALS["EMAIL_HASH_SALT"] =
$GLOBALS["USERNAME_ENCRYPT_KEY"] =
 -->
