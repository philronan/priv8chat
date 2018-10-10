SET NAMES 'utf8';
DROP DATABASE IF EXISTS `secure_chat_db`;
CREATE DATABASE `secure_chat_db` DEFAULT CHARACTER SET utf8;
GRANT ALL ON `secure_chat_db`.* TO 'secure_chat_admin@localhost' IDENTIFIED BY 'todo';
FLUSH PRIVILEGES;
USE `secure_chat_db`;

CREATE TABLE users (
    `user_id`                   BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username_enc`              CHAR(64) UNIQUE NOT NULL,
    `username_hash`             CHAR(32) UNIQUE NOT NULL,
    `email_hash`                CHAR(32) UNIQUE NOT NULL,
    `password_hash`             CHAR(64) NOT NULL,
    `password_reset_date`       DATETIME NOT NULL,
    `password_reset_nonce_enc`  CHAR(32) NOT NULL,
    `cookie_token_hash`         CHAR(32) NOT NULL,
    `cookie_expiry_date`        DATETIME NOT NULL,
    INDEX (`email_hash`(8)),
    INDEX (
) DEFAULT CHARACTER SET utf8;

CREATE TABLE messages (
    `message_id`                BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `from_user_id`              BIGINT NOT NULL,
    `to_user_id`                BIGINT NOT NULL,
    `sent_date`                 DATETIME NOT NULL,
    `message_subject_enc`       TEXT NOT NULL,
    `message_body_enc`          MEDIUMTEXT NOT NULL,
    `read_flag`                 BOOL NOT NULL DEFAULT 0,
    `sender_deleted`            BOOL NOT NULL DEFAULT 0,
    `receiver_deleted`          BOOL NOT NULL DEFAULT 0,
    INDEX (`from_user_id`),
    INDEX (`to_user_id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE contacts (
    `list_owner_user_id`        BIGINT NOT NULL PRIMARY KEY,
    `contact_user_id`           BIGINT NOT NULL,
    UNIQUE (`list_owner_user_id`, `contact_user_id`)
);
