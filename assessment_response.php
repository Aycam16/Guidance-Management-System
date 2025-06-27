<?php
require_once 'database.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assessmentId = $_POST['assessment_id'] ?? null;

    if (!$assessmentId) {
        echo "Invalid assessment submission.";
        exit;
    }

    // Collect responses
    $responses = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'response_') === 0) {
            $questionId = str_replace('response_', '', $key);
            $responses[$questionId] = htmlspecialchars($value);
        }
    }

    if (empty($responses)) {
        echo "No responses received!";
        exit;
    }

    $serializedResponses = json_encode($responses);

    // Get Student ID from session or set to default (e.g., 0)
    $studentId = $_SESSION['student_id'] ?? null; // Replace with default `0` if needed

    if ($studentId === null) {
        echo "Error: Student not logged in!";
        exit;
    }

    // Insert into the database
    $query = "INSERT INTO assesment_response (Assesment_ID, Student_ID, response_data, submitted_at) 
              VALUES (?, ?, ?, NOW())";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iss", $assessmentId, $studentId, $serializedResponses);

    if (mysqli_stmt_execute($stmt)) {
        echo "Your responses have been submitted successfully!";
        // Redirect if needed
        // header("Location: thank_you.php");
    } else {
        echo "Error saving responses.";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request method.";
}

mysqli_close($conn);
?>
