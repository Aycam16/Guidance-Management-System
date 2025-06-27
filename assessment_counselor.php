<?php

session_start();
require 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index_public.php');
    exit();
}

// Ensure the user has the 'counselor' role
if ($_SESSION['role'] !== 'counselor') {
    echo "Access denied: You do not have permission to view this page.";
    exit();
}

// Check for logout request
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Destroy the session and redirect to a public page
    session_unset();
    session_destroy();
    header("Location: index_public.php"); // Redirect to public page after logout
    exit();
}

// Handle actions: edit or delete
if (isset($_GET['action']) && isset($_GET['id'])) {
    $assessmentId = $_GET['id'];

    // Edit action
    if ($_GET['action'] == 'edit') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['formTitle'];
            $description = $_POST['formDescription'];

            $updateQuery = "UPDATE assesment SET title = ?, description = ?, updated_at = NOW() WHERE Assesment_ID = ?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $assessmentId);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: assessment_counselor.php");
                exit;
            } else {
                error_log(mysqli_error($conn));  // Log the error
                echo "Something went wrong. Please try again later.";
            }
        }

        // Fetch assessment details for editing
        $query = "SELECT * FROM assesment WHERE Assesment_ID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $assessmentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $assessment = mysqli_fetch_assoc($result);
    }

    // Delete action
    if ($_GET['action'] == 'delete') {
        $deleteQuery = "DELETE FROM assesment WHERE Assesment_ID = ?";
        $stmt = mysqli_prepare($conn, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $assessmentId);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: assessment_counselor.php");
            exit;
        } else {
            error_log(mysqli_error($conn));  // Log the error
            echo "Something went wrong. Please try again later.";
        }
    }
}

// Handle new assessment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action'])) {
    $title = $_POST['formTitle'];
    $description = $_POST['formDescription'];
    $created_by = 'Admin'; // Adjust based on logged-in user

    $insertQuery = "INSERT INTO assesment (title, description, created_by, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sss", $title, $description, $created_by);

    if (mysqli_stmt_execute($stmt)) {
        $assessmentId = mysqli_insert_id($conn); // Get the inserted assessment ID

        // Insert each question
        if (!empty($_POST['questions'])) {
            foreach ($_POST['questions'] as $questionData) {
                $questionText = $questionData['text'];
                $response = $questionData['response'];

                $insertQuestionQuery = "INSERT INTO assesment_questions (assesment_id, question_text, response_option) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insertQuestionQuery);
                mysqli_stmt_bind_param($stmt, "iss", $assessmentId, $questionText, $response);

                if (!mysqli_stmt_execute($stmt)) {
                    error_log(mysqli_error($conn));
                    echo "Error inserting question.";
                }
            }
        }

        header("Location: assessment_counselor.php");
        exit;
    } else {
        error_log(mysqli_error($conn));  // Log the error
        echo "Something went wrong. Please try again later.";
    }
}

// Fetch all assessments to display in the table
$query = "SELECT * FROM assesment";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Stay updated with the latest announcements from QCU Guidance and Counseling.">
    <title>QCU Guidance and Counseling</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: grid;
            grid-template-rows: auto 1fr auto;
            font-family: Arial, sans-serif;
            overflow-x: hidden;
        }

        body {
        margin-bottom: 450px; /* Add space to prevent overlapping of content with footer */
         }

        .content-wrapper {
            flex: 1; 
            padding-bottom: 20px; /* Add spacing between content and footer */
        }

        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
            width: 100%;
            overflow-x: hidden;
        }

        .navbar {
            padding: 0.5rem 1rem;
        }

        .navbar-brand img {
            margin-right: 10px; /* Spacing for brand image */
        }

        .navbar-nav .nav-item {
            margin-left: 15px; /* Space between nav items */
        }

        .navbar-nav .nav-link {
            color: #ffffff !important; /* Ensure white links */
        }

        /* Align the profile dropdown to the right */
        .navbar-nav .dropdown-menu {
            width: 250px; /* Fixed width */
            min-width: 250px; /* Ensure minimum width */
            max-height: 400px; /* Set max-height if the dropdown has many items */
            overflow-y: auto; /* Enable scrolling if too many items */
            position: absolute; /* Ensure it positions itself correctly */
            top: 100%; /* Align it below the dropdown toggle */
            left: 50%; /* Center horizontally */
            transform: translateX(-50%); /* Adjust position so it aligns perfectly */
            z-index: 1050; /* Ensure it's above other content */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Optional: Add a shadow for better visibility */
        }

        /* Ensure dropdown aligns to the right edge of the parent */
        .navbar-nav .dropdown-menu-end {
            left: auto;
            right: 0; /* Align the dropdown to the right edge of the parent */
        }

        /* Optional: Adjust padding for the dropdown items */
        .navbar-nav .dropdown-item {
            padding: 10px 15px;
        }

        /* Optional: Adjust the hover effect */
        .navbar-nav .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        /* Optional: Adjust the size of the dropdown toggle icon */
        .navbar-nav .dropdown-toggle {
            padding: 0.5rem 1rem;
        }

        .navbar-nav .fa-envelope {
            font-size: 18px; /* Size for envelope icon */
        }

        @media (max-width: 576px) {
            .carousel-item {
                background-size: cover;
                height: 350px;
            }
            .navbar-nav {
                text-align: center;
            }
            .navbar-nav .nav-item {
                margin-left: 5px;
                margin-right: 5px;
            }
        }
       
        footer {
            background-color: #003366;
            color: white;
            padding: 20px;
            margin-top: auto;
        }

        footer.text-center {
            background-color: #004E98;
            color: white;
            padding: 10px;
        }

        .btn-primary,
        .btn-outline-secondary,
        .btn-secondary {
            background-color: #004E98;
            border: none;
            color: white;
        }

        .btn-primary:hover,
        .btn-outline-secondary:hover,
        .btn-secondary:hover {
            background-color: #003366;
            color: white;
        }

        table thead th {
            background-color: #D9E7FF;
            color: #004E98;
            text-align: center;
        }

        table tbody td {
            text-align: center;
        }

        .status-posted {
            background-color: #D4F4DD;
            color: #2D8F47;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 0.9em;
        }

         .filters-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filters-container .left-controls {
            display: flex;
            gap: 10px;
        }

        .filters-container .right-control {
            text-align: right;
        }

        /* Pagination Buttons */
        .pagination .page-link {
            color: #004E98; /* Text color */
        }

        .pagination .page-item.active .page-link {
            background-color: #004E98; /* Active background color */
            border-color: #004E98;
            color: white; /* Text color for active button */
        }

        .pagination .page-link:hover {
            color: #003366; /* Darker hover color */
        }

        .status-posted {
            background-color: #D4F4DD;
            color: #2D8F47;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <nav class="navbar navbar-expand-lg" style="background-color: #004E98;">
            <a class="navbar-brand d-flex align-items-center" href="#" style="color: white;">
                <img src="QCU_Logo.png" alt="QCU Logo" style="height: 50px; width: 50px; margin-right: 10px;">
                QCU Guidance and Counseling
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index_counselor.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="module_counselor.php">Modules</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Services</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="assessment_counselor.php">Assessment</a>
                            <a class="dropdown-item" href="appointment_counselor.php">Counseling Appointment</a>
                            <a class="dropdown-item" href="moral_counselor.php">Good Moral Certificate</a>
                            <a class="dropdown-item" href="feedback_counselor.php">Share Your Feedback</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </a>
                    <div class="dropdown-menu">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <a class="dropdown-item" href="profile_counselor.php">Profile</a>
                            <a class="dropdown-item" href="?logout=true">Logout</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="login.php">Login</a>
                        <?php endif; ?>
                    </div>

                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">Assessments</h1>

        <!-- Add Assessment Modal Trigger -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAssessmentModal">Add Assessment</button>

        <!-- Add Assessment Modal -->
        <div class="modal fade" id="addAssessmentModal" tabindex="-1" aria-labelledby="addAssessmentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAssessmentModalLabel">Add New Assessment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="assessment_counselor.php" method="POST">
                            <label for="formTitle">Title:</label>
                            <input type="text" id="formTitle" name="formTitle" class="form-control" required>

                            <label for="formDescription" class="mt-3">Description:</label>
                            <textarea id="formDescription" name="formDescription" class="form-control" required></textarea>

                            <!-- Likert Scale Questions -->
                            <div id="questionsContainer">
                                <div class="mb-3">
                                    <label for="question1Text" class="form-label">Question 1</label>
                                    <input type="text" class="form-control" id="question1Text" name="questions[1][text]" placeholder="Enter your question" required>

                                    <div class="d-flex justify-content-between">
                                        <label>
                                            <input type="radio" name="questions[1][response]" value="Strongly Disagree" required>
                                            Strongly Disagree
                                        </label>
                                        <label>
                                            <input type="radio" name="questions[1][response]" value="Disagree">
                                            Disagree
                                        </label>
                                        <label>
                                            <input type="radio" name="questions[1][response]" value="Neutral">
                                            Neutral
                                        </label>
                                        <label>
                                            <input type="radio" name="questions[1][response]" value="Agree">
                                            Agree
                                        </label>
                                        <label>
                                            <input type="radio" name="questions[1][response]" value="Strongly Agree">
                                            Strongly Agree
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary" id="addQuestionButton">Add Another Question</button>
                            <button type="submit" class="btn btn-primary mt-3">Save Assessment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       <!-- Display Assessments Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Ensure data is sanitized before outputting
                $title = htmlspecialchars($row['title']);
                $description = htmlspecialchars($row['description']);
                $created_by = htmlspecialchars($row['created_by']);
                $created_at = htmlspecialchars($row['created_at']);
                $assessment_id = htmlspecialchars($row['Assesment_ID']);
                $is_archived = isset($row['is_archived']) ? $row['is_archived'] : 0; // Default to 0 if null
                
                // Use full if-else instead of ternary operator
                if ($is_archived == 1) {
                    $status = 'Archived';
                    $badge_class = 'bg-danger';
                } else {
                    $status = 'Active';
                    $badge_class = 'bg-success';
                }
                
                echo "<tr>";
                echo "<td>{$title}</td>";
                echo "<td>{$description}</td>";
                echo "<td>{$created_by}</td>";
                echo "<td>{$created_at}</td>";
                echo "<td><span class='badge {$badge_class}'>{$status}</span></td>";
                echo "<td>
                        <a href='?action=edit&id={$assessment_id}' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='?action=delete&id={$assessment_id}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this assessment?\")'>Delete</a>
                        <a href='view_response_ass.php?id={$assessment_id}' class='btn btn-info btn-sm'>View Responses</a> <!-- Added View Responses button -->
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No assessments found.</td></tr>";
        }
        ?>
    </tbody>
</table>

    </div>

    <!-- Main Footer -->
<footer >
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="QCU_Logo.png" alt="Quirino University Logo" style="width: 80px; height: 80px; margin-right: 30px;">
            <div class="text-left">
            <p>Email: <a href="mailto:guidance.unit@qcu.edu.ph" class="text-light">guidance.unit@qcu.edu.ph</a></p>
            <p><a href="https://www.facebook.com/qcuguidanceunit" class="text-light">Follow us on Facebook</a></p>
            </div>
        </div>
        <div class="text-left">
            <p>Email: <a href="mailto:your-email@example.com" class="text-light">your-email@example.com</a></p>
            <p><a href="https://facebook.com" class="text-light">Follow us on Facebook</a></p>
        </div>
    </div>
</footer>

<!-- Copyright Footer -->
<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 QCU Guidance and Counseling</p>
    </div>
</footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let questionCount = 1;

        document.getElementById('addQuestionButton').addEventListener('click', function() {
            questionCount++;
            const questionContainer = document.getElementById('questionsContainer');
            const newQuestion = `
                <div class="mb-3">
                    <label for="question${questionCount}Text" class="form-label">Question ${questionCount}</label>
                    <input type="text" class="form-control" id="question${questionCount}Text" name="questions[${questionCount}][text]" placeholder="Enter your question" required>
                    <div class="d-flex justify-content-between">
                        <label>
                            <input type="radio" name="questions[${questionCount}][response]" value="Strongly Disagree" required>
                            Strongly Disagree
                        </label>
                        <label>
                            <input type="radio" name="questions[${questionCount}][response]" value="Disagree">
                            Disagree
                        </label>
                        <label>
                            <input type="radio" name="questions[${questionCount}][response]" value="Neutral">
                            Neutral
                        </label>
                        <label>
                            <input type="radio" name="questions[${questionCount}][response]" value="Agree">
                            Agree
                        </label>
                        <label>
                            <input type="radio" name="questions[${questionCount}][response]" value="Strongly Agree">
                            Strongly Agree
                        </label>
                    </div>
                </div>
            `;
            questionContainer.insertAdjacentHTML('beforeend', newQuestion);
        });
    </script>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>