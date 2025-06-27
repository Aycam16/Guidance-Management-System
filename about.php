<?php
// Connect to the database
session_start();
require_once 'database.php'; // Assuming you have a db_connection.php file to manage DB connection

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$role = $_SESSION['role'] ?? 'guest';


// Check for logout request
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Destroy the session and redirect to a public page
    session_unset();
    session_destroy();
    header("Location: index_public.php"); // Redirect to public page after logout
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Stay updated with the latest announcements from QCU Guidance and Counseling.">
    <title>QCU Guidance and Counseling</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        html, body {
                height: 100%;
                margin: 0;
                display: flex;
                flex-direction: column;
                font-family: Arial, sans-serif;
                box-sizing: border-box; /* Ensures padding and borders don't cause overflow */
            }

            body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            overflow-x: hidden; /* Prevent horizontal overflow */
            }

        main {
            flex: 1;
            padding: 20px;
        }

        .carousel-inner {
            height: 575px;
        }

        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 20px 0;
            }

            footer.text-center {
                background-color: #004E98;
                color: white;
                padding: 10px;
            }

            .navbar {
            padding: 0.5rem 1rem;
        }

        .navbar-nav {
            margin-left: auto; /* Ensures the navbar content is aligned to the right */
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

        .btn-warning:hover {
            background-color: #FFC107;
            color: black;
        }

        /* General Styles for About Us Section */
        .about-us {
            padding: 50px 20px;
            background-color: #f9f9f9;
        }

        /* Background image header */
        .about-us-header {
            background-image: url('Aboutpic.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 50vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
        }

        .about-us-header .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            max-width: 80%;
            margin: 0 auto;
        }

        .about-us-header h1 {
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .about-us-header p {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Main Section */
        .about-us {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
        }

        /* Vision Section */
        .vision {
            margin-top: 20px;
            text-align: center;
        }

        .vision h2 {
            font-size: 28px;
            color: #004E98;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .vision p {
            font-size: 18px;
            color: #555;
            text-align: center;
        }

        .vision ul {
            font-size: 16px;
            color: #555;
            list-style-type: square;
            margin-left: 20px;
            text-align: center;
        }

        /* Mission Section */
        .mission {
            margin-top: 20px;
            text-align: center;
        }

        .mission h2 {
            font-size: 28px;
            color: #004E98;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .mission p {
            font-size: 18px;
            color: #555;
            text-align: center;
        }

        .mission ul {
            font-size: 16px;
            color: #555;
            list-style-type: square;
            margin-left: 20px;
            text-align: center;
        }

        /* Objectives Section */
        .objectives {
            margin-top: 40px;
            text-align: center;
        }

        .objectives h2 {
            font-size: 28px;
            color: #004E98;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .objectives ul {
            font-size: 16px;
            color: #555;
            list-style-type: square;
            margin-left: 20px;
            text-align: center;
        }

        /* Services Section */
        .services {
            margin-top: 40px;
            text-align: center;
        }

        .services h2 {
            font-size: 28px;
            color: #004E98;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .services ul {
            font-size: 16px;
            color: #555;
            list-style-type: none;
            padding-left: 0;
            text-align: center;
        }

        .services li {
            margin-bottom: 15px;
        }

        .services strong {
            color: #004E98;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .vision-mission {
                flex-direction: column;
                align-items: center;
            }

            .vision, .mission {
                width: 100%;
                margin-bottom: 20px;
            }
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
            <ul class="navbar-nav ms-auto"> <!-- ms-auto aligns the navbar items to the right -->
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

    <main>
        <div class="container mt-5">
            <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;"">About Us</h1>
            <section class="about-us">
                <div class="about-us-header"></div>

                <div class="vision">
                    <h2>Our Vision</h2>
                    <p>The GCU envisions to develop the fullest potential of the students to participate effectively in complex societies and globalized workforce.</p>
                </div>

                <div class="mission">
                    <h2>Our Mission</h2>
                    <ul>
                        <li>Provide quality guidance services.</li>
                        <li>Promote healthy academic and career choices.</li>
                        <li>Facilitate awareness on mental health and well-being.</li>
                    </ul>
                </div>

                <div class="objectives">
                    <h2>Our Objectives</h2>
                    <ul>
                        <li>Help students develop their academic potential.</li>
                        <li>Provide counseling services for personal and social issues.</li>
                    </ul>
                </div>

                <div class="services">
                    <h2>Our Services</h2>
                    <ul>
                        <li><strong>Counseling:</strong> Guidance for emotional and psychological well-being.</li>
                        <li><strong>Workshops:</strong> Organize activities for self-growth and development.</li>
                        <li><strong>Assessment:</strong> Psychological testing and evaluation.</li>
                    </ul>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer >
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
</div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
