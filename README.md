# Health_Plus

Health_Plus is a comprehensive web application that helps users manage their health and wellness. The platform allows users to track key health activities, plan their diets, participate in health-related discussions, and gain insights through data-driven analytics.

---

## Features

1. **Health Activity Tracking**  
   - Track daily steps, water intake, sleep hours, mood, and exercise time.  
   - Visualize health progress with analytics.

2. **Meal Planning and Calorie Goals**  
   - Log meals with details like name, type, and calorie count.  
   - Set calorie goals and receive prompts when goals are exceeded.

3. **Health Discussions**  
   - Share and discuss health topics with a supportive community.  
   - Comment on posts, like comments, and delete your own posts or comments.

4. **Health Quiz**  
   - Answer engaging quiz questions to learn more about health-related topics.  
   - Admin can manage quiz questions and categories.

5. **Admin Features**  
   - Manage health discussion topics and comments with options to delete inappropriate content.

---

## Technologies Used

### Frontend
- **HTML** and **CSS**: For structure and styling.
- **JavaScript** (AJAX): For dynamic interactions and asynchronous communication with the backend.
- **TailWindCss**: For responsive design and modern UI elements.

### Backend
- **PHP**: For handling server-side logic and CRUD operations.
- **MySQL**: For storing and managing user data.

### Additional Tools
- **JSON**: For structured data handling in quizzes.
- **Regular Expressions**: For robust data validation on both frontend and backend.

---

## Database Design

### Key Tables
1. **HealthUsers**: Stores user details.
2. **Health_Activities**: Logs daily health activities.
3. **Meals**: Tracks meal details and calorie intake.
4. **Health_Topics**: Manages health discussion topics.
5. **Comments**: Handles user comments on discussion topics.
6. **Quiz_Questions**: Stores quiz questions, options, and correct answers.

---

## Installation

1. Clone the repository:  
   ```bash
   git clone https://github.com/iipenaky/health_plus.git
   ```

2. Import the SQL file to your MySQL server to set up the database.

3. Update the `db.php` file with your database credentials.

4. Host the application on a live server (e.g., XAMPP, LAMP, or a cloud platform).

5. Access the application through the browser at the server's IP/URL.

---

## Usage Instructions

### For Users
1. Register and log in to access features.
2. Log daily activities and meals.
3. Participate in health discussions.
4. Attempt quizzes to enhance health knowledge.
5. Plan your meals

### For Admins
1. Manage health topics, comments and users.
2. Create and edit quiz questions.

---

## Live Demo

The application is hosted on a live server. Access it at:  
[**http://169.239.251.102:3341/~peniel.ansah/health_plus/frontend/index.html**](http://169.239.251.102:3341/~peniel.ansah/health_plus/frontend/index.html)

---

## Video Demo

Watch a walkthrough of the application here:  
[**Video Link**](##pending)

---

## Developer Information

- **Author**: Peniel  Maame Akyireko Ansah
- **Project Name**: Health_Plus  
- **Course**: CS341: Web Technologies (First Semester, Sep-Dec 2024)  
- **Faculty**: Govindha Yeluripati  

---
