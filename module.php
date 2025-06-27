<?php
// Connect to the database
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

// Fetch all modules with status 'post'
$sql = "SELECT ModuleID, Title, Description, FilePath FROM modules WHERE Status = 'post'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Stay updated with the latest modules from QCU Guidance and Counseling.">
    <title>QCU Guidance and Counseling</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
        overflow-x: hidden; /* Ensure no horizontal scroll */
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    /* Ensure enough spacing below the navbar */
    .content-wrapper {
        flex: 1;
        padding: 20px;
        padding-top: 100px; /* Adjust according to the navbar height */
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

    /* Modules Grid Layout */
/* Module Wrapper */
.module-wrapper {
    max-width: 960px; /* Restrict the width of the entire module section */
    margin: 0 auto; /* Center the wrapper */
    background-color: #f8f9fa; /* Optional: subtle background for the section */
    padding: 20px 15px;
    border-radius: 10px; /* Rounded corners for the wrapper */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    margin-top: 20px; /* Add spacing below the heading */
}

/* Adjust the heading's position */
h1 {
    margin-top: 20px; /* Add a little space from the navbar */
    margin-bottom: 20px; /* Reduce spacing from the module wrapper */
}
/* Card Styling */
.card {
    background-color: rgba(221, 230, 242, 1); /* Light blue background */
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden; /* Clip content for rounded corners */
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); /* Shadow effect on hover */
}

/* Image Styling Inside Cards */
.card-img-top {
    max-width: 100%;
    height: auto;
    padding: 15px;
    object-fit: contain; /* Ensures aspect ratio */
}

/* Card Content Styling */
.card-body {
    padding: 15px;
}

.card-title {
    font-size: 18px;
    font-weight: bold;
    color: #004E98;
    margin-bottom: 10px;
}

.card-text {
    font-size: 14px;
    color: #333;
    margin-bottom: 20px;
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

    .btn-primary {
    background-color: #004E98;
    border-color: #004E98;
    font-size: 14px;
    padding: 8px 15px;
}

.btn-primary:hover {
    background-color: #003366;
    border-color: #003366;
}

/* Responsive Layout */
@media (max-width: 768px) {
    .module-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Adjust column width */
    }
}
    </style>
</head>
<body>
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

<!-- Main Content -->
 
<div class="content-wrapper">
    <div class="container">
        <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">COUNSELLING MODULES</h1>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '    <div class="card">';
                    echo '        <div class="card-body">';
                    echo '            <h5 class="card-title">' . htmlspecialchars($row['Title']) . '</h5>';
                    echo '            <p class="card-text">' . htmlspecialchars($row['Description']) . '</p>';
                    echo '            <a href="view_module.php?id=' . htmlspecialchars($row['ModuleID']) . '" class="btn btn-primary" target="_blank">View Module</a>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center">No modules available at the moment.</p>';
            }
            ?>
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
</body>
</html>
