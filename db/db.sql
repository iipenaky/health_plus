-- Drop all existing tables if they exist
DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Health_Topics;
DROP TABLE IF EXISTS Quiz_Questions;
DROP TABLE IF EXISTS Health_Tips;
DROP TABLE IF EXISTS Profile_Settings;
DROP TABLE IF EXISTS Health_Quiz;
DROP TABLE IF EXISTS Journal_Entries;
DROP TABLE IF EXISTS Exercise_Routines;
DROP TABLE IF EXISTS Meals;
DROP TABLE IF EXISTS Health_Activities;
DROP TABLE IF EXISTS HealthUsers;

-- Use the database
USE webtech_fall2024_peniel_ansah;

-- Create HealthUsers Table
CREATE TABLE IF NOT EXISTS HealthUsers (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    calorie_goal INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Health_Activities Table
CREATE TABLE IF NOT EXISTS Health_Activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_date DATE NOT NULL,
    steps INT DEFAULT 0,
    water_intake DECIMAL(5,2) DEFAULT 0.00,
    sleep_hours DECIMAL(4,2) DEFAULT 0.00,
    exercise_time DECIMAL(4,2) DEFAULT 0.00,
    mood VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Meals Table
CREATE TABLE IF NOT EXISTS Meals (
    meal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    meal_date DATE NOT NULL,
    meal_name VARCHAR(255) NOT NULL,
    calories INT DEFAULT 0,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack'),
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Exercise_Routines Table
CREATE TABLE IF NOT EXISTS Exercise_Routines (
    routine_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    routine_name VARCHAR(255) NOT NULL,
    duration DECIMAL(4,2) DEFAULT 0.00,
    intensity ENUM('low', 'medium', 'high') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Journal_Entries Table
CREATE TABLE IF NOT EXISTS Journal_Entries (
    journal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    entry_date DATE NOT NULL,
    entry_text TEXT,
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Health_Quiz Table
CREATE TABLE IF NOT EXISTS Health_Quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_date DATE NOT NULL,
    score INT DEFAULT 0,
    personalized_tips TEXT,
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Profile_Settings Table
CREATE TABLE IF NOT EXISTS Profile_Settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    health_goal VARCHAR(255),
    notification_preferences JSON DEFAULT ('{"email":true, "sms":true, "reminders":true}'),
    privacy ENUM('public', 'private') DEFAULT 'public',
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Health_Tips Table
CREATE TABLE IF NOT EXISTS Health_Tips (
    tip_id INT AUTO_INCREMENT PRIMARY KEY,
    tip_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create Quiz_Questions Table
CREATE TABLE IF NOT EXISTS Quiz_Questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    options JSON NOT NULL,
    correct_option CHAR(1),
    category VARCHAR(50) DEFAULT 'general'
);

-- Create Health_Topics Table
CREATE TABLE IF NOT EXISTS Health_Topics (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);

-- Create Comments Table
CREATE TABLE IF NOT EXISTS Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES Health_Topics(topic_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES HealthUsers(user_id) ON DELETE CASCADE
);
