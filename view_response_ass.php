<?php
require_once 'database.php'; // Ensure you have a valid DB connection in this file

// Start the session
session_start();

// Check for logout request
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_unset();
    session_destroy();
    header("Location: index_public.php"); // Redirect to public page after logout
    exit();
}

// Check if assessment_id is provided in the URL and validate it
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $assessment_id = (int) $_GET['id'];

    // Fetch the assessment details
    $assessmentQuery = "SELECT * FROM assesment WHERE Assesment_ID = ?";
    if ($stmt = mysqli_prepare($conn, $assessmentQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $assessment_id);
        mysqli_stmt_execute($stmt);
        $assessmentResult = mysqli_stmt_get_result($stmt);

        if ($assessment = mysqli_fetch_assoc($assessmentResult)) {
            // Assessment found
        } else {
            echo "Assessment not found.";
            exit;
        }
    } else {
        echo "Error preparing the query: " . mysqli_error($conn);
        exit;
    }

    // Fetch the questions for the assessment
    $questionsQuery = "SELECT * FROM assesment_questions WHERE assesment_id = ?";
    if ($stmt = mysqli_prepare($conn, $questionsQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $assessment_id);
        mysqli_stmt_execute($stmt);
        $questionsResult = mysqli_stmt_get_result($stmt);
    } else {
        echo "Error preparing the query: " . mysqli_error($conn);
        exit;
    }

    // Fetch responses for each question (if responses exist)
    $responsesQuery = "SELECT * FROM assesment_response WHERE assesment_id = ?";
    if ($responsesStmt = mysqli_prepare($conn, $responsesQuery)) {
        mysqli_stmt_bind_param($responsesStmt, "i", $assessment_id);
        mysqli_stmt_execute($responsesStmt);
        $responsesResult = mysqli_stmt_get_result($responsesStmt);

        // Organize responses by question
        $responsesByQuestion = [];
        while ($response = mysqli_fetch_assoc($responsesResult)) {
            $responsesByQuestion[$response['question_id']][] = $response['response'];
        }
    } else {
        echo "Error preparing the query: " . mysqli_error($conn);
        exit;
    }
} else {
    echo "Invalid or missing assessment ID.";
    exit;
}
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
        margin-bottom: 250px; /* Add space to prevent overlapping of content with footer */
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
        td a.btn {
            display: inline-block;
            margin: auto;
            text-align: center;
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
                            <a class="dropdown-item" href="profile.php">Profile</a>
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

        <h1>Responses for: <?= htmlspecialchars($assessment['title']) ?></h1>
        <h2>Assessment Details</h2>
        <p><strong>Description:</strong> <?= htmlspecialchars($assessment['description']) ?></p>
        <p><strong>Created By:</strong> <?= htmlspecialchars($assessment['created_by']) ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($assessment['created_at']) ?></p>

        <h2>Questions and Responses</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Responses</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($question = mysqli_fetch_assoc($questionsResult)): ?>
                    <tr>
                        <td><?= htmlspecialchars($question['question_text']) ?></td>
                        <td>
                            <?php
                            $questionId = $question['Question_ID'];
                            if (isset($responsesByQuestion[$questionId])) {
                                echo implode(", ", $responsesByQuestion[$questionId]);
                            } else {
                                echo "No responses yet.";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Student Assessment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Student Name:</strong> <span id="studentName"></span></p>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Answer</th>
                        </tr>
                    </thead>
                    <tbody id="assessmentAnswers">
                        <!-- Dynamic content will be injected here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    </div>


    <!-- Footer -->
    <footer>
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="QCU_Logo.png" alt="QCU Logo" style="width: 80px; height: 80px; margin-right: 30px;">
                <div>
                    <h5>Guidance and Counseling Unit</h5>
                    <p>Quirino Highway, San Bartolome, Novaliches, Quezon City<br>Mon - Fri 8am - 5pm</p>
                </div>
            </div>
            <div>
            <p>Email: <a href="mailto:guidance.unit@qcu.edu.ph" class="text-light">guidance.unit@qcu.edu.ph</a></p>
            <p><a href="https://www.facebook.com/qcuguidanceunit" class="text-light">Follow us on Facebook</a></p>
            </div>
        </div>
    </footer>
    <footer class="text-center">
        <p>&copy; 2024 QCU Guidance and Counseling</p>
    </footer>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
    function loadAssessmentData(studentName) {
        // Update the student name
        document.getElementById('studentName').innerText = studentName;

        // Example dynamic content for answers
        const answers = [
            { question: "Question 1", answer: "Strongly Agree" },
            { question: "Question 2", answer: "Agree" },
            { question: "Question 3", answer: "Neutral" },
            { question: "Question 4", answer: "Disagree" },
            { question: "Question 5", answer: "Strongly Disagree" },
        ];

        // Clear the existing answers
        const answersTable = document.getElementById('assessmentAnswers');
        answersTable.innerHTML = '';

        // Populate the answers table
        answers.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${item.question}</td><td>${item.answer}</td>`;
            answersTable.appendChild(row);
        });
    }
</script>
<script> 
    function sendEmail(studentName, studentEmail) {
    // Example payload for sending email
    const emailData = {
        name: studentName,
        email: studentEmail,
        subject: "Your Assessment Results",
        message: "Here are your assessment results. Thank you for your submission!"
    };

    // Call your backend endpoint to send email
    fetch('/send-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(emailData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Email sent successfully to ${studentName}`);
        } else {
            alert(`Failed to send email to ${studentName}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the email.');
    });
}
</script>
</body>
</html>
