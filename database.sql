CREATE TABLE IF NOT EXISTS site_config (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    page_clicker BIGINT UNSIGNED NOT NULL DEFAULT 0,
    sitealert VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_config (id, page_clicker, sitealert)
VALUES (1, 0, '')
ON DUPLICATE KEY UPDATE id = id;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    coins BIGINT UNSIGNED NOT NULL DEFAULT 50,
    gender ENUM('male', 'female', 'nonbinary') NOT NULL DEFAULT 'nonbinary',
    role ENUM('user', 'mod', 'admin', 'owner') NOT NULL DEFAULT 'user',
    joindate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS games (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    description VARCHAR(255) NOT NULL DEFAULT '',
    active_players INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY games_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO games (name, description, active_players)
VALUES
    ('Crossroads', 'Classic brick battle placeholder.', 0),
    ('Baseplate', 'A blank place ready for building.', 0),
    ('Sword Fight', 'Retro combat test place.', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);
