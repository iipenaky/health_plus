-- Create Database
CREATE DATABASE IF NOT EXISTS health_plus;

-- Use the health_plus database
USE health_plus;

-- USERS TABLE
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user', -- Role for differentiating users and admins
    int calorie_goal,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- HEALTH ACTIVITIES TABLE
CREATE TABLE Health_Activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_date DATE NOT NULL,
    steps INT DEFAULT 0,
    water_intake DECIMAL(5,2) DEFAULT 0.00, -- Liters
    sleep_hours DECIMAL(4,2) DEFAULT 0.00, -- Hours
    exercise_time DECIMAL(4,2) DEFAULT 0.00, -- Hours
    mood VARCHAR(50), -- Optional column for tracking mood
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- MEALS TABLE
CREATE TABLE Meals (
    meal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    meal_date DATE NOT NULL,
    meal_name VARCHAR(255) NOT NULL,
    calories INT DEFAULT 0,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack'), -- Consistent meal types
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- EXERCISE ROUTINES TABLE
CREATE TABLE Exercise_Routines (
    routine_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    routine_name VARCHAR(255) NOT NULL,
    duration DECIMAL(4,2) DEFAULT 0.00, -- Hours
    intensity ENUM('low', 'medium', 'high') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- JOURNAL ENTRIES TABLE
CREATE TABLE Journal_Entries (
    journal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    entry_date DATE NOT NULL,
    entry_text TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- HEALTH QUIZ TABLE
CREATE TABLE Health_Quiz (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_date DATE NOT NULL,
    score INT DEFAULT 0,
    personalized_tips TEXT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- SETTINGS TABLE
CREATE TABLE Profile_Settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    health_goal VARCHAR(255), -- Customizable health goal
    notification_preferences JSON DEFAULT ('{"email":true, "sms":true, "reminders":true}'),
    privacy ENUM('public', 'private') DEFAULT 'public',
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- CONTENT MANAGEMENT: HEALTH TIPS TABLE
CREATE TABLE Health_Tips (
    tip_id INT AUTO_INCREMENT PRIMARY KEY,
    tip_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- CONTENT MANAGEMENT: QUIZ QUESTIONS TABLE
CREATE TABLE Quiz_Questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    options JSON NOT NULL, -- Example: '{"A": "Option 1", "B": "Option 2", "C": "Option 3"}'
    correct_option CHAR(1), -- Stores correct answer (e.g., 'A')
    category VARCHAR(50) DEFAULT 'general'
);


CREATE TABLE Health_Topics (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES Health_Topics(topic_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);
