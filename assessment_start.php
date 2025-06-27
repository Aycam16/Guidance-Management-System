<?php
session_start();
require_once 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index_public.php');
    exit();
}

// Ensure the user has the 'counselor' role
if ($_SESSION['role'] !== 'student') {
    echo "Access denied: You do not have permission to view this page.";
    exit();
}

// Check if an assessment ID is passed via URL
if (isset($_GET['id'])) {
    $assessmentId = $_GET['id'];

    // Fetch assessment details
    $query = "SELECT * FROM assesment WHERE Assesment_ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $assessmentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $assessment = mysqli_fetch_assoc($result);

    // Check if assessment exists
    if (!$assessment) {
        echo "Assessment not found!";
        exit;
    }

    // Fetch the associated questions for the assessment
    $queryQuestions = "SELECT * FROM assesment_questions WHERE Assesment_ID = ?";
    $stmtQuestions = mysqli_prepare($conn, $queryQuestions);
    mysqli_stmt_bind_param($stmtQuestions, "i", $assessmentId);
    mysqli_stmt_execute($stmtQuestions);
    $questionsResult = mysqli_stmt_get_result($stmtQuestions);
    $questions = mysqli_fetch_all($questionsResult, MYSQLI_ASSOC);
} else {
    echo "Invalid request!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Start your assessment for QCU Guidance and Counseling">
    <title>Start Assessment</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom Styles */
        .assessment-container {
            margin-top: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-primary {
            background-color: #004E98;
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background-color: #003366;
        }

        .question-container {
            margin-bottom: 20px;
        }

        .question-container label {
            display: block;
        }
        .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        background-color: #004E98;
    }

    .navbar-nav .nav-item {
        margin-left: 15px;
    }

    .navbar-nav .nav-link {
        color: #ffffff !important;
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
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="module.php">Modules</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Services</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="assessment.php">Assessment</a>
                        <a class="dropdown-item" href="appointment.php">Counseling Appointment</a>
                        <a class="dropdown-item" href="goodmoral.php">Good Moral Certificate</a>
                        <a class="dropdown-item" href="feedback.php">Share Your Feedback</a>
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

    <!-- Assessment Start Container -->
    <div class="container assessment-container">
        <h1 class="text-center" style="color: #004E98; font-weight: bold;">Start Your Assessment</h1>
        <div class="card">
            <div class="card-body">
                <h3 class="card-title"><?php echo htmlspecialchars($assessment['title']); ?></h3>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($assessment['description'])); ?></p>

                <!-- Form for answering all questions -->
                <form method="POST" action="assessment_response.php">
                    <input type="hidden" name="assessment_id" value="<?php echo $assessmentId; ?>">

                    <?php foreach ($questions as $question): ?>
                        <div class="question-container">
                            <p><strong><?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                            
                            <!-- Likert scale options for each question -->
                            <div>
                                <label><input type="radio" name="response_<?php echo $question['Question_ID']; ?>" value="Strongly Disagree" required> Strongly Disagree</label>
                                <label><input type="radio" name="response_<?php echo $question['Question_ID']; ?>" value="Disagree"> Disagree</label>
                                <label><input type="radio" name="response_<?php echo $question['Question_ID']; ?>" value="Neutral"> Neutral</label>
                                <label><input type="radio" name="response_<?php echo $question['Question_ID']; ?>" value="Agree"> Agree</label>
                                <label><input type="radio" name="response_<?php echo $question['Question_ID']; ?>" value="Strongly Agree"> Strongly Agree</label>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary mt-2">Submit Responses</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
