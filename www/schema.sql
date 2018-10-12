DROP TABLE IF EXISTS priv8users;
DROP TABLE IF EXISTS priv8messages;
DROP TABLE IF EXISTS priv8nonce;

CREATE TABLE priv8users (
    user_id             INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username_encrypted  VARCHAR(256) NOT NULL UNIQUE,
    email_hashed        VARCHAR(160) NOT NULL UNIQUE KEY,
    password            VARCHAR(72) NOT NULL,
    conf_link_sent      TIMESTAMP NOT NULL DEFAULT 0,
    conf_link_clicked   TIMESTAMP NOT NULL DEFAULT 0,
    is_registered       BOOLEAN NOT NULL DEFAULT FALSE
) DEFAULT CHARACTER SET ascii;

CREATE TABLE priv8messages (
    message_id          BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    from_user           INT NOT NULL,
    to_user             INT NOT NULL,
    when_sent           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    message_text        LONGTEXT NOT NULL,
    has_been_read       BOOLEAN NOT NULL DEFAULT FALSE,
    sender_deleted      BOOLEAN NOT NULL DEFAULT FALSE,
    receiver_deleted    BOOLEAN NOT NULL DEFAULT FALSE
) DEFAULT CHARACTER SET ascii;
ALTER TABLE priv8messages ADD INDEX from_user (from_user);
ALTER TABLE priv8messages ADD INDEX to_user (to_user);
ALTER TABLE priv8messages ADD INDEX when_sent (when_sent);

CREATE TABLE priv8nonce (
    id                  BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    created             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE priv8nonce ADD INDEX created (created);
