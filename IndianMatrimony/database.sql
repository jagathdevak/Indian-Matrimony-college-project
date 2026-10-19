CREATE DATABASE IF NOT EXISTS indian_matrimony
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE indian_matrimony;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    dob DATE NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    religion VARCHAR(80) DEFAULT 'Not specified',
    caste VARCHAR(100) DEFAULT 'Not specified',
    education VARCHAR(150) DEFAULT 'Not specified',
    profession VARCHAR(150) DEFAULT 'Not specified',
    bio TEXT,
    photo VARCHAR(255) DEFAULT NULL,
    banned TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS banned_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    reason VARCHAR(255) DEFAULT NULL,
    banned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Preserve emails of any legacy accounts already marked as banned.
INSERT IGNORE INTO banned_users (email, reason)
SELECT email, 'Migrated permanent ban'
FROM users
WHERE banned=1;

DELETE FROM users WHERE banned=1;


CREATE TABLE IF NOT EXISTS interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending','accepted','declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_interest (sender_id, receiver_id),
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users
(name,email,password,gender,dob,city,state,religion,caste,education,profession,bio,photo,banned)
SELECT
'Ananya Nair',
'ananya@example.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC7cVQqR2QvQ4qQ9Qy6',
'Female',
'2001-04-18',
'Kochi',
'Kerala',
'Hindu',
'Nair',
'B.Tech Computer Science',
'Software Engineer',
'Love travel, books, music and meaningful conversations. Looking for a kind and ambitious partner.',
NULL,
0
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email='ananya@example.com'
);

INSERT INTO users
(name,email,password,gender,dob,city,state,religion,caste,education,profession,bio,photo,banned)
SELECT
'Arjun Menon',
'arjun@example.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC7cVQqR2QvQ4qQ9Qy6',
'Male',
'1999-11-02',
'Bengaluru',
'Karnataka',
'Hindu',
'Menon',
'MBA',
'Product Manager',
'Calm, family-oriented and career driven. Weekend explorer and cricket fan.',
NULL,
0
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email='arjun@example.com'
);

INSERT INTO users
(name,email,password,gender,dob,city,state,religion,caste,education,profession,bio,photo,banned)
SELECT
'Meera Sharma',
'meera@example.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC7cVQqR2QvQ4qQ9Qy6',
'Female',
'2000-08-27',
'Mumbai',
'Maharashtra',
'Hindu',
'Brahmin',
'CA',
'Chartered Accountant',
'Independent, warm and close to family. I enjoy cooking, art and discovering new places.',
NULL,
0
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email='meera@example.com'
);

INSERT INTO users
(name,email,password,gender,dob,city,state,religion,caste,education,profession,bio,photo,banned)
SELECT
'Rohan Kapoor',
'rohan@example.com',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC7cVQqR2QvQ4qQ9Qy6',
'Male',
'1998-02-13',
'Delhi',
'Delhi',
'Hindu',
'Khatri',
'B.Tech',
'Entrepreneur',
'Building a startup and trying to stay curious. Looking for a genuine life partner.',
NULL,
0
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email='rohan@example.com'
);

INSERT INTO admins (name,email,password)
SELECT
'Vivah Administrator',
'admin@vivah.local',
'$2y$12$EbzuPO96qunCTuVBr77Kj.5Dis5ysQXDV1GRDA5aCbbfd5PekM48u'
WHERE NOT EXISTS (
    SELECT 1 FROM admins
    WHERE email='admin@vivah.local'
);
