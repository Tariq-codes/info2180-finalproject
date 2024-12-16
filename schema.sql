-- Database: 'dolphin_crm.sql'

DROP DATABASE IF EXISTS dolphin_crm;
CREATE DATABASE dolphin_crm;
USE dolphin_crm;

-- Table for 'users'
CREATE TABLE `users` (
    `id` int(11) NOT NULL auto_increment,
    `firstname` varchar(255) NOT NULL default '',
    `lastname` varchar(255) NOT NULL default '',
    `password` varchar(255) NOT NULL default '',
    `email` varchar(255) NOT NULL default '',
    `role` varchar(255) NOT NULL default '',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

-- Table for 'contacts'
CREATE TABLE `contacts` (
    `id` int(11) NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `firstname` varchar(255) NOT NULL default '',
    `lastname` varchar(255) NOT NULL default '',
    `email` varchar(255) NOT NULL default '',
    `telephone` varchar(255) NOT NULL default '',
    `company` varchar(255) NOT NULL default '',
     type ENUM('Sales Lead', 'Support') NOT NULL,
    `assigned_to` int(11) NOT NULL,
    `created_by` int(11) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
);

-- Table for 'notes'
CREATE TABLE `notes` (
    `id` int(11) NOT NULL auto_increment,
    `contact_id` int(11) NOT NULL,
    `comment` text NOT NULL default '',
    `created_by` int(11) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
);

-- Insert Admin User with Hashed Password
INSERT INTO `users` (`firstname`, `lastname`, `email`, `password`, `role`, `created_at`)
VALUES ('Admin', 'User', 'admin@project2.com', SHA2('password123', 256), 'Admin', CURRENT_TIMESTAMP);
