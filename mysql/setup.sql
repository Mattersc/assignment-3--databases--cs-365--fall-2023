-- drop password database if it already exists, then create new password database
DROP DATABASE IF EXISTS student_passwords;

CREATE DATABASE student_passwords DEFAULT CHARACTER SET utf8mb4;

CREATE USER 'passwords_user'@'localhost' IDENTIFIED BY 'themuhman84!';

GRANT ALL PRIVILEGES ON student_passwords.* TO 'passwords_user'@'localhost';

-- generate key from my last name
SET @key_str = UNHEX(1986049382758275679);

-- use the passwords database
USE student_passwords;

CREATE TABLE IF NOT EXISTS websites (
    website_id INT AUTO_INCREMENT,
    website_name VARCHAR(64) NOT NULL,
    website_url VARCHAR(128) NOT NULL,
    PRIMARY KEY (website_id)
);

-- create a table for storing info
CREATE TABLE IF NOT EXISTS accounts (
    account_id INT AUTO_INCREMENT,
    website_id INT NOT NULL,
    username VARCHAR(25) NOT NULL,
    email VARCHAR(256) NOT NULL,
    password VARBINARY(256) NOT NULL,
    comment TEXT,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username(username, website_id),
    PRIMARY KEY (account_id)
);

INSERT INTO websites (website_name, website_url) VALUES
    ('Google', 'www.Google.com'),
    ('Github', 'www.Github.com'),
    ('Youtube', 'www.Youtube.com'),
    ('Spotify', 'www.Spotify.com'),
    ('Instagram', 'www.Instagram.com');

-- insert data into  table
INSERT INTO accounts (website_id, username, email, password, comment) VALUES
    (1, 'JosephB', 'JoeB@gmail.com', AES_ENCRYPT('Itiswhatitis', @key_str), 'google account'),
    (2, 'vadimism', 'Vadim@gmail.com', AES_ENCRYPT('mrcool18', @key_str), 'github account'),
    (3, 'hyderP', 'hydes@gmail.com', AES_ENCRYPT('Iamsupersmart', @key_str), 'youtube account'),
    (4, 'Leeeeeroy', 'Jenkins@gmail.com', AES_ENCRYPT('leeeeeeeeeroy', @key_str), 'spotify account'),
    (5, 'TommyB', 'Tomms@gmail.com', AES_ENCRYPT('thatguy', @key_str),'instagram account');
