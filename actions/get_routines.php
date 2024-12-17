<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // If not logged in or not a 'user', send a JSON response indicating redirection
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}

// Include the database connection
include '../db/db.php';

// Check if the required POST parameters 'goal' and 'experience' are set
if (isset($_POST['goal']) && isset($_POST['experience'])) {
    // Get the logged-in user's ID, goal, and experience level from the POST data
    $user_id = $_SESSION['user_id'];
    $goal = $_POST['goal'];
    $experience = $_POST['experience'];

    // Define the available routines based on goal and experience level
    $routines = [
        'strength' => [
            'beginner' => [
                'routine_name' => 'Beginner Strength Training',
                'duration' => 0.75,
                'intensity' => 'low',
                'exercises' => [
                    ['Push-Ups', 10],
                    ['Squats', 15],
                    ['Plank', 5],
                ]
            ],
            'intermediate' => [
                'routine_name' => 'Intermediate Strength Workout',
                'duration' => 1.00,
                'intensity' => 'medium',
                'exercises' => [
                    ['Bench Press', 20],
                    ['Deadlifts', 20],
                    ['Pull-Ups', 10],
                ]
            ],
            'advanced' => [
                'routine_name' => 'Advanced Strength Challenge',
                'duration' => 1.50,
                'intensity' => 'high',
                'exercises' => [
                    ['Barbell Squats', 30],
                    ['Clean and Jerk', 20],
                    ['Snatch', 20],
                ]
            ],
        ],
        'endurance' => [
            'beginner' => [
                'routine_name' => 'Beginner Endurance Builder',
                'duration' => 1.00,
                'intensity' => 'low',
                'exercises' => [
                    ['Jogging', 20],
                    ['Jump Rope', 15],
                    ['Cycling', 30],
                ]
            ],
            'intermediate' => [
                'routine_name' => 'Intermediate Cardio Blast',
                'duration' => 1.25,
                'intensity' => 'medium',
                'exercises' => [
                    ['Running', 30],
                    ['Stair Climbing', 20],
                    ['Swimming', 40],
                ]
            ],
            'advanced' => [
                'routine_name' => 'Advanced Endurance Challenge',
                'duration' => 2.00,
                'intensity' => 'high',
                'exercises' => [
                    ['Marathon Training', 60],
                    ['Hill Sprints', 30],
                    ['Rowing', 45],
                ]
            ],
        ],
        'flexibility' => [
            'beginner' => [
                'routine_name' => 'Beginner Flexibility Intro',
                'duration' => 0.50,
                'intensity' => 'low',
                'exercises' => [
                    ['Yoga', 15],
                    ['Stretching', 10],
                    ['Child Pose', 5],
                ]
            ],
            'intermediate' => [
                'routine_name' => 'Intermediate Flexibility Flow',
                'duration' => 1.00,
                'intensity' => 'medium',
                'exercises' => [
                    ['Yoga Flow', 30],
                    ['Pilates', 20],
                    ['Foam Rolling', 15],
                ]
            ],
            'advanced' => [
                'routine_name' => 'Advanced Flexibility Master',
                'duration' => 1.50,
                'intensity' => 'high',
                'exercises' => [
                    ['Advanced Yoga', 45],
                    ['Dynamic Stretching', 30],
                    ['AcroYoga', 40],
                ]
            ],
        ],
    ];

    // Get the selected routine based on the goal and experience level
    $selected_routine_data = $routines[$goal][$experience] ?? null;

    if ($selected_routine_data) {
        // If the routine is valid, attempt to save it to the database
        try {
            // Prepare the SQL statement to insert the new routine into the Exercise_Routines table
            $stmt = $conn->prepare("INSERT INTO Exercise_Routines (user_id, routine_name, duration, intensity) VALUES (?, ?, ?, ?)");

            // Bind the parameters to the SQL query
            $stmt->bind_param(
                "isds", // 'i' for integer, 's' for string, 'd' for double/float
                $user_id, // User ID
                $selected_routine_data['routine_name'], // Routine name
                $selected_routine_data['duration'], // Duration of the routine
                $selected_routine_data['intensity'] // Intensity of the routine
            );

            // Execute the query
            if ($stmt->execute()) {
                // Get the last inserted routine ID
                $routine_id = $stmt->insert_id;

                // Prepare the response data
                $response = [
                    'routine_id' => $routine_id,
                    'routine_name' => $selected_routine_data['routine_name'],
                    'duration' => $selected_routine_data['duration'],
                    'intensity' => $selected_routine_data['intensity'],
                    'routine' => $selected_routine_data['exercises']
                ];

                // Return the response as JSON
                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'Failed to save routine. Please try again.']);
            }

            // Close the statement
            $stmt->close();
        } catch (Exception $e) {
            // Log the error and return a generic error message
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['error' => 'Failed to save routine. Please try again.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid routine selection']);
    }
} else {
    echo json_encode(['error' => 'Invalid input']);
}
?>
