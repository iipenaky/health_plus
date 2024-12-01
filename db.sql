-- Create Database
CREATE DATABASE IF NOT EXISTS health_plus;

-- Use the health_plus database
USE health_plus;

-- Create Users Table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Health_Activities Table
CREATE TABLE Health_Activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE NOT NULL,
    steps INT,
    water_intake DECIMAL(5,2),
    sleep_hours DECIMAL(4,2),
    meal_calories INT,
    exercise_time DECIMAL(4,2),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Meals Table
CREATE TABLE Meals (
    meal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    meal_date DATE NOT NULL,
    meal_name VARCHAR(255) NOT NULL,
    calories INT,
    meal_type VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Exercise_Routines Table
CREATE TABLE Exercise_Routines (
    routine_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    routine_name VARCHAR(255) NOT NULL,
    duration DECIMAL(4,2),
    intensity VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Journal Entries Table
CREATE TABLE Journal_Entries (
    journal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    entry_date DATE NOT NULL,
    entry_text TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Health_Quiz Table
CREATE TABLE Health_Quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_date DATE NOT NULL,
    score INT,
    personalized_tips TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Admin_Statistics Table
CREATE TABLE Admin_Statistics (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,
    metric_type VARCHAR(50) NOT NULL,
    value INT NOT NULL,
    stat_date DATE NOT NULL
);

-- Create Profile_Settings Table
CREATE TABLE Profile_Settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    password_reset BOOLEAN DEFAULT FALSE,
    health_goal VARCHAR(255),
    notifications BOOLEAN DEFAULT TRUE,
    privacy VARCHAR(50) DEFAULT 'public',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Admin_Users Table
CREATE TABLE Admin_Users (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);
