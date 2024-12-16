<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['redirect' => '../frontend/index.html']);
    exit();
}
include '../db/db.php';


if (isset($_POST['goal']) && isset($_POST['experience'])) {
    $user_id = $_SESSION['user_id'];
    $goal = $_POST['goal'];
    $experience = $_POST['experience'];

    // Routine generation logic with intensity and duration
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

    $selected_routine_data = $routines[$goal][$experience] ?? null;

    if ($selected_routine_data) {
        try {
            $stmt = $conn->prepare("INSERT INTO Exercise_Routines (user_id, routine_name, duration, intensity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param(
                "isds",
                $user_id,
                $selected_routine_data['routine_name'],
                $selected_routine_data['duration'],
                $selected_routine_data['intensity']
            );
            if ($stmt->execute()) {
                $routine_id = $stmt->insert_id;
                $response = [
                    'routine_id' => $routine_id,
                    'routine_name' => $selected_routine_data['routine_name'],
                    'duration' => $selected_routine_data['duration'],
                    'intensity' => $selected_routine_data['intensity'],
                    'routine' => $selected_routine_data['exercises']
                ];

                echo json_encode($response);
            } else {
                echo json_encode(['error' => 'Failed to save routine. Please try again.']);
            }
            $stmt->close();
        } catch (Exception $e) {
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
