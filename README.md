# User_data_collection
Everything inside one single file (user_data.php) instead of multiple files. That file should
Create the Database:
CREATE DATABASE IF NOT EXISTS user_data;

USE user_data;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    middle_name VARCHAR(50),
    last_name VARCHAR(50),
    age INT,
    sex ENUM('Male','Female','Other'),
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    vill VARCHAR(100),
    post VARCHAR(100),
    ps VARCHAR(100),
    dist VARCHAR(100),
    pin_code VARCHAR(10),
    state VARCHAR(100),
    country VARCHAR(50) DEFAULT 'India'
);
Run this SQL to create database + table:
