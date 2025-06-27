<?php
session_start();
require_once 'database.php'; // Adjust to your DB connection file

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

// Check for logout request
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Destroy the session and redirect to a public page
    session_unset();
    session_destroy();
    header("Location: index_public.php"); // Redirect to public page after logout
    exit();
}

// Fetch all assessments to display in the table
$query = "SELECT * FROM assesment";
$result = mysqli_query($conn, $query);

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create and manage assessments in QCU Guidance and Counseling.">
    <title>QCU Guidance and Counseling - Assessments</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        /* Ensure body takes full height */
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

        main {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Footer Styles */
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px;
        }

        footer.text-center {
            background-color: #004E98;
            color: white;
            padding: 10px;
        }

        /* Custom container to center and control width */
        .container-custom {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Custom Assessment Container */
        .assessment-container {
            width: 100%;
            max-width: 350px; /* Control max width */
            margin: 20px auto;
            padding: 15px;
            background-color: white;
            border-radius: 10px;
            border: 3px solid #004E98;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease-in-out;
        }

        /* Title style inside assessment container */
        .assessment-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #004E98;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Description of the assessment */
        .assessment-description {
            font-size: 1rem;
            color: #555;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Style for the Start button */
        .start-button {
            background-color: #004E98;
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            display: block;
            margin: 0 auto;
            transition: background-color 0.3s;
        }

        .start-button:hover {
            background-color: #003366;
        }

        /* Grid system for responsive display of assessment cards */
        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        /* Hover effect to scale up the card */
        .assessment-container:hover {
            transform: scale(1.05);
        }

        /* Responsive styling for smaller screens */
        @media (max-width: 768px) {
            .assessment-container {
                width: 100%;
                margin: 10px 0;
            }
        }

        .dropdown-menu {
            width: 250px; /* Width of dropdown */
        }

        .dropdown-item {
            white-space: nowrap; /* Prevent text wrapping */
            padding: 10px 15px; /* Padding for better spacing */
            text-align: left; /* Align text to the left */
        }

        .btn-primary:hover {
            background-color: #003366; /* Darker shade on hover */
            color: white; /* Text color on hover */
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
    </style>
</head>
<body>
<!-- Header Section -->
<header>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #004E98;">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="QCU_Logo.png" alt="QCU Logo" style="height: 50px; width: 50px; margin-right: 10px;">
            QCU Guidance and Counseling
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="module.php">Modules</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">
                        Services
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item" href="assessment.php">Assessment</a></li>
                        <li><a class="dropdown-item" href="appointment.php">Counseling Appointment</a></li>
                        <li><a class="dropdown-item" href="goodmoral.php">Good Moral Certificate</a></li>
                        <li><a class="dropdown-item" href="feedback.php">Share Your Feedback</a></li>
                    </ul>
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

<div class="content-wrapper">
    <main>
    <div class="container mt-5">
        <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">Available Assessments</h1>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="assessment-container">
                    <h2 class="assessment-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                    <p class="assessment-description"><?php echo htmlspecialchars($row['description']); ?></p>
                    <a href="assessment_start.php?id=<?php echo $row['Assesment_ID']; ?>" class="start-button">Start</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No active assessments available at the moment.</p>
        <?php endif; ?>
    </div>
    </main>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
