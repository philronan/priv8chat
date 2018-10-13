<?php

if (!$VIA_INDEX) die(''); // No output unless accessed via /index.php

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

// Default values to use if nothing provided by user
$db_name = "priv8chat";
$db_user = "priv8chat_admin";
$db_password = randomPassword(20);


// Check the MySQL details provided by the user. If we can access the
// database and have permission to write these settings to a config
// file, then the setup is complete.

$errors = [];

while ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("$ROOT/zxcvbn-loader.php");
    $db_name = @$_POST['db-name'];
    $db_user = @$_POST['db-user'];
    $db_password = @$_POST['db-password'];
    // Max length of DB/user name = 64 chars (https://dev.mysql.com/doc/refman/5.7/en/identifiers.html#idm140258615329280)
    if (preg_match('/^[0-9A-Za-z_-]{1,64}$/', $db_name) != 1) {
        $errors[] = _("The database name can only contain up to 64 ASCII letters, digits, underscores and hyphens");
    }
    // Unbelievable, but in MySQL v.5.6.34 on my iMac, the max user name length is just 16 characters!
    if (preg_match('/^[0-9A-Za-z_-]{1,16}$/', $db_user) != 1) {
        $errors[] = _("The database username can only contain up to 16 ASCII letters, digits, underscores and hyphens");
    }
    // Maximum password length = 32 (https://stackoverflow.com/q/7465204/1679849)
    if (preg_match('/^[\x20-\x7e]{8,32}$/', $db_password) != 1) {
        $errors[] = _("The database password must consist of 8–32 printable ASCII characters/spaces");
    }
    elseif (!is_strong_password($db_password)) {
        $errors[] = _("Please choose a stronger password");
    }
    if (count($errors) > 0) break;

    // The credentials provided by the user look OK. Let's see if they work...
    $mysqli = @mysqli_init();
    if (!$mysqli) {
        $errors[] = _("Unable to initialize MySQL connection. Please check your server configuration");
        break;
    }
    if (!@mysqli_real_connect($mysqli, 'localhost', $db_user, $db_password, $db_name)) {
        $errors[] = sprintf(_("Can't connect to MySQL: %s"), mysqli_connect_error());
        break;
    }
    if (!@$mysqli->set_charset("utf8")) {
        $errors[] = sprintf(_("Can't change MySQL charset to utf8: %s"), $mysqli->error);
        break;
    }

    // **TODO** Parsing the output of "SHOW GRANTS" would give a better
    // picture of the MySQL user's capabilities, but the following should
    // suffice for the time being.
    $tmp_table = randomPassword(25);
    if (!@$mysqli->query("CREATE TABLE `$tmp_table` (`x` INT);")) {
        $errors[] = sprintf(_("Can't create MySQL table: %s"), $mysqli->error);
        break;
    }
    if (!@$mysqli->query("INSERT INTO `$tmp_table` (`x`) VALUES (123),(456);")) {
        $errors[] = sprintf(_("Can't insert into MySQL table: %s"), $mysqli->error);
        break;
    }
    $result = @$mysqli->query("SELECT SUM(`x`) FROM `$tmp_table` WHERE 1;");
    if (!$result) {
        $errors[] = sprintf(_("Can't select from MySQL table: %s"), $mysqli->error);
        break;
    }
    if ($result->num_rows != 1 || mysqli_fetch_row($result)[0] != 579) {
        $errors[] = _("Can't read from MySQL table");
        break;
    }
    // Not particularly bothered if this fails, so don't check for errors:
    @$mysqli->query("DROP TABLE `$tmp_table`;");

    // Now create the database
    @$mysqli->query("DROP TABLE IF EXISTS priv8users;");
    @$mysqli->query("DROP TABLE IF EXISTS priv8messages;");
    @$mysqli->query("DROP TABLE IF EXISTS priv8nonce;");

    @$mysqli->query("CREATE TABLE priv8users (" .
        "user_id             INT NOT NULL AUTO_INCREMENT PRIMARY KEY," .
        "username_encrypted  VARCHAR(256) NOT NULL UNIQUE," .
        "email_hashed        VARCHAR(160) NOT NULL UNIQUE KEY," .
        "password            VARCHAR(72) NOT NULL," .
        "conf_link_sent      TIMESTAMP NOT NULL DEFAULT 0," .
        "conf_link_clicked   TIMESTAMP NOT NULL DEFAULT 0," .
        "is_registered       BOOLEAN NOT NULL DEFAULT FALSE" .
    ") DEFAULT CHARACTER SET ascii;");

    @$mysqli->query("CREATE TABLE priv8messages (" .
        "message_id          BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY," .
        "from_user           INT NOT NULL," .
        "to_user             INT NOT NULL," .
        "when_sent           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP," .
        "message_subject     TEXT NOT NULL," .
        "message_text        LONGTEXT NOT NULL," .
        "has_been_read       BOOLEAN NOT NULL DEFAULT FALSE," .
        "sender_deleted      BOOLEAN NOT NULL DEFAULT FALSE," .
        "receiver_deleted    BOOLEAN NOT NULL DEFAULT FALSE" .
    ") DEFAULT CHARACTER SET ascii;");
    @$mysqli->query("ALTER TABLE priv8messages ADD INDEX from_user (from_user);");
    @$mysqli->query("ALTER TABLE priv8messages ADD INDEX to_user (to_user);");
    @$mysqli->query("ALTER TABLE priv8messages ADD INDEX when_sent (when_sent);");

    @$mysqli->query("CREATE TABLE priv8nonce (" .
        "id                  BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY," .
        "created             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP" .
    );");
    @$mysqli->query("ALTER TABLE priv8nonce ADD INDEX created (created);");



    // OK, the MySQL account is working. Now we just need to store these
    // credentials in a .config file...
    if (!is_writable($ROOT)) {
        $chown_help = "";
        if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
            $nixuser = posix_getpwuid(posix_getuid())['name'];
            $nixgroup = posix_getgrgid(posix_getgid())['name'];
            $chown_help = sprintf(_("<br>(Try typing <code>sudo chown %s:%s %s</code> at a command prompt.)"), $nixuser, $nixgroup, $ROOT);
        }
        $errors[] = sprintf(_("Can't modify %s. Please check the directory permissions"), $ROOT) . $chown_help;
        break;
    }
    // Create a directory for session data
    if (!(is_dir("$ROOT/.sessions") && is_writable("$ROOT/.sessions")) && !mkdir("$ROOT/.sessions", 0700)) {
        $errors[] = sprintf(_("Can't create session data directory in %s. Please check the directory permissions"), $ROOT);
        break;
    }
    $master_key = bin2hex(openssl_random_pseudo_bytes(16));
    $pass_64 = base64_encode($db_password);
    $config_txt = "DBNAME='$db_name'\nDBUSER='$db_user'\nDBPASS='$pass_64'\nMASTERKEY='$master_key'\n";
    if (!@file_put_contents("$ROOT/.config", $config_txt)) {
        $errors[] = sprintf(_("Can't create config file at %s. Please check the file/directory permissions"), "$ROOT/.config");
        break;
    }
    if (@file_get_contents("$ROOT/.config") !== $config_txt) {
        $errors[] = sprintf(_("Can't read back config file from %s. Please check the file permissions"), "$ROOT/.config");
        break;
    }


    // Redirect to home page
    header("HTTP/1.1 303 See Other");
    header("Location: https://" . $_SERVER['HTTP_HOST'] . "/");
    die();
}


// Sanitise user data for safe inclusion in HTML
$db_name = htmlspecialchars($db_name);
$db_user = htmlspecialchars($db_user);
$db_password = htmlspecialchars($db_password);

// Expand error messages if present
$error_report = "";
if (count($errors) > 0) {
    $error_report = "<div class=\"alert alert-danger\">\n<ul>\n<p>";
    $error_report .= _("Sorry, but you need to fix the following error(s):");
    $error_report .= "</p>\n<ul>\n<li>";
    $error_report .= implode("</li>\n<li>", $errors);
    $error_report .= "</li>\n</ul>\n</div>";
}

$page_title = 'Setup';
$page_content = <<<END_PAGE
<div class="jumbotron text-center">
  <h1>🔒Priv8Chat</h1>
  <p>A secure online messaging system</p>
</div>
<div class="container">
  <div class="row mb-1">
      <div class="col-sm-12">
      <p class="lead">You're nearly there! All you need to do now is set up a
          database for this application, and fill in the details below. (The
          database name and password have been pre-filled, but you can change
          these values if you like. Just be sure to use a strong password.)</p>
$error_report
      </div>
  </div>
  <div class="row">
    <div class="col-sm-12">
      <form action="/setup" method="POST">
      <div class="form-group">
        <label for="db-name">Database name:</label>
        <input type="text" class="form-control" id="db-name" name="db-name"
          placeholder="(ASCII letters, digits, underscores and hyphens only. 64 character limit.)"
          title="ASCII letters, digits, underscores and hyphens only. 64 character limit"
          pattern="[0-9A-Za-z_-]{1,}" required
          value="$db_name">
      </div>
      <div class="form-group">
        <label for="db-user">Database user:</label>
        <input type="text" class="form-control" id="db-user" name="db-user"
          placeholder="(ASCII letters, digits, underscores and hyphens only. 16 character limit.)"
          title="ASCII letters, digits, underscores and hyphens only. 16 character limit"
          pattern="[0-9A-Za-z_-]{1,}" required
          value="$db_user">
      </div>
      <div class="form-group">
        <label for="db-password">Database password:</label>
        <input type="text" class="form-control" id="db-password" name="db-password"
          placeholder="(Printable ASCII charcters and spaces only. From 8 to 32 characters.)"
          title="Printable ASCII and spaces only. From 8 to 32 characters"
          pattern="[ -~]{8,}" required
          value="$db_password">
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-default">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>
END_PAGE;

include "$ROOT/views/main.php";
