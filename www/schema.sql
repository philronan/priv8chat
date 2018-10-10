DROP DATABASE IF EXISTS sec_chat;
CREATE DATABASE sec_chat DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;

USE sec_chat;
GRANT ALL ON sec_chat.* TO sec_chat_admin@localhost IDENTIFIED BY "0a5d47523a01087731c2c14bdb7155b4";
GRANT LOCK TABLES, SELECT ON sec_chat.* TO sec_chat_read_only@localhost IDENTIFIED BY "hunter2";
FLUSH PRIVILEGES;

CREATE TABLE users (
    user_id             INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username_encrypted  TINYTEXT NOT NULL,
    email               VARCHAR(160) NOT NULL UNIQUE KEY,
    password            VARCHAR(72) NOT NULL,
    conf_secret         CHAR(32) NOT NULL,
    conf_link_sent      DATETIME DEFAULT NULL,
    conf_link_clicked   DATETIME DEFAULT NULL,
    is_registered       TINYINT NOT NULL DEFAULT 0,
    last_active         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX hash_prefix ON users (username_hash(8));

CREATE TABLE messages (
    message_id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    from_user           INT NOT NULL,
    to_user             INT NOT NULL,
    when_sent           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message_text        TEXT NOT NULL,
    has_been_read       TINYINT NOT NULL DEFAULT 0,
    sender_deleted      TINYINT NOT NULL DEFAULT 0,
    receiver_deleted    TINYINT NOT NULL DEFAULT 0
);

CREATE TABLE sessions (
    session_token       CHAR(32) NOT NULL PRIMARY KEY,
    ipv4                INT UNSIGNED NOT NULL,
    ajax_name_lookup    VARCHAR(64),
    started             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_active         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
