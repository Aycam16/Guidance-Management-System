<?php
session_start();
require 'database.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: index_public.php');
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

// Query to count total students
$sql_students = "SELECT COUNT(*) AS total_students FROM tbl_users WHERE role = 'student'";
$result_students = $conn->query($sql_students);
$total_students = $result_students->fetch_assoc()['total_students'];

// Query to count total counselors
$sql_counselors = "SELECT COUNT(*) AS total_counselors FROM tbl_users WHERE role = 'counselor'";
$result_counselors = $conn->query($sql_counselors);
$total_counselors = $result_counselors->fetch_assoc()['total_counselors'];

// Query to count total employees
$sql_employees = "SELECT COUNT(*) AS total_employees FROM tbl_users WHERE role = 'employee'";
$result_employees = $conn->query($sql_employees);
$total_employees = $result_employees->fetch_assoc()['total_employees'];

// Close the connection
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .card-icon {
            font-size: 80px; /* Larger icons */
            color: #004E98;
        }

        .overview-section .card {
            background-color: #f8f9fa;
            border: none;
            text-align: center;
            margin: 15px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .overview-section h6 {
            font-size: 1.5rem; /* Larger title */
        }

        .overview-section h3 {
            font-size: 3rem; /* Larger numbers */
            font-weight: bold;
        }

        .overview-section {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 30px;
        }

        /* Make sure the title spans the full width */
        .overview-section h1 {
            width: 100%;
            text-align: center;
            color: #004E98;
            font-weight: bold;
        }

        .manage-section .btn {
            border-radius: 15px;
            text-align: center;
            background-color: #e9f5fc;
            border: 2px solid #004E98;
            color: #004E98;
            font-weight: bold;
            font-size: 1.5rem; /* Larger button text */
            padding: 30px 20px; /* Larger padding */
            transition: background-color 0.3s, transform 0.2s ease;
        }

        .manage-section .btn:hover {
            background-color: #004E98;
            color: white;
            transform: scale(1.1); /* Enlarge on hover */
        }

        h5, h6 {
            color: #004E98;
            text-transform: uppercase;
        }

        footer {
            background-color: #004E98;
            color: white;
            padding: 20px;
            font-size: 1.2rem; /* Larger footer text */
        }

        .row > div {
            margin-bottom: 30px; /* Add spacing between rows */
        }

        .custom-icon-color {
            color: #004E98; /* Custom color for icons */
        }

        .container {
            max-width: 1200px; /* Optional: Limit max width */
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
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #004E98; color: white;">
        <div class="container-fluid">
            <!-- Navbar Brand -->
            <a class="navbar-brand text-white" href="#">
                <i class="fas fa-shield-alt"></i> Admin Dashboard
            </a>

            <!-- Toggler for mobile view -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
                    
                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </a>
                    <div class="dropdown-menu">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <a class="dropdown-item" href="profile_admin.php">Profile</a>
                            <a class="dropdown-item" href="?logout=true">Logout</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="login.php">Login</a>
                        <?php endif; ?>
                    </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

    <!-- Main Content -->
    <main class="container mt-5">
        <!-- Overview of User Accounts -->
<section class="overview-section">
    <h1>OVERVIEW OF USER ACCOUNTS</h1>
    <div class="row justify-content-center">
        <div class="col-md-3">
            <div class="card">
                <i class="fas fa-users card-icon"></i>
                <h6>Total Students</h6>
                <h3><?php echo number_format($total_students); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <i class="fas fa-user-tie card-icon"></i>
                <h6>Total Counselors</h6>
                <h3><?php echo number_format($total_counselors); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <i class="fas fa-chalkboard-teacher card-icon"></i>
                <h6>Total Employees</h6>
                <h3><?php echo number_format($total_employees); ?></h3>
            </div>
        </div>
    </div>
</section>

        <div class="container text-center mt-5">
            <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">MANAGE ACCOUNTS</h1>
            <div class="row mt-4 justify-content-center">
                <!-- Students Button -->
                <div class="col-md-2">
                    <a href="students.php" class="btn btn-light shadow-sm w-100 py-5 fs-4">
                        <i class="fas fa-user-graduate fa-3x custom-icon-color"></i>
                        <p class="mt-3 mb-0">Students</p>
                    </a>
                </div>

                <!-- Counselors Button -->
                <div class="col-md-2">
                    <a href="counselors.php" class="btn btn-light shadow-sm w-100 py-5 fs-4">
                        <i class="fas fa-user-tie fa-3x custom-icon-color"></i>
                        <p class="mt-3 mb-0">Counselors</p>
                    </a>
                </div>

                <!-- Employees Button -->
                <div class="col-md-2">
                    <a href="employees.php" class="btn btn-light shadow-sm w-100 py-5 fs-4">
                        <i class="fas fa-chalkboard-teacher fa-3x custom-icon-color"></i>
                        <p class="mt-3 mb-0">Employee</p>
                    </a>
                </div>

                <!-- Admins Button -->
                <div class="col-md-2">
                    <a href="admin_user.php" class="btn btn-light shadow-sm w-100 py-5 fs-4">
                        <i class="fas fa-shield-alt fa-3x custom-icon-color"></i>
                        <p class="mt-3 mb-0">Admins</p>
                    </a>
                </div>
            </div>
        </div>
    </main>

   
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

