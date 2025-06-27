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


// Fetch aggregated data with error handling
$query = "
    SELECT 
        category, 
        statement, 
        ROUND(SUM(CASE WHEN response = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS strongly_disagree,
        ROUND(SUM(CASE WHEN response = 2 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS disagree,
        ROUND(SUM(CASE WHEN response = 3 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS neutral,
        ROUND(SUM(CASE WHEN response = 4 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS agree,
        ROUND(SUM(CASE WHEN response = 5 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) AS strongly_agree
    FROM feedback
    GROUP BY category, statement
";

$result = mysqli_query($conn, $query);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
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
        <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">FEEDBACK RESULT</h1>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group ms-auto" style="max-width: 300px;">
                <input type="text" class="form-control" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-secondary" type="button">Search</button>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Category</th>
                    <th>Statement</th>
                    <th>Strongly Disagree</th>
                    <th>Disagree</th>
                    <th>Neutral</th>
                    <th>Agree</th>
                    <th>Strongly Agree</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td><?php echo htmlspecialchars($row['statement']); ?></td>
                        <td><?php echo round($row['strongly_disagree'], 2) . '%'; ?></td>
                        <td><?php echo round($row['disagree'], 2) . '%'; ?></td>
                        <td><?php echo round($row['neutral'], 2) . '%'; ?></td>
                        <td><?php echo round($row['agree'], 2) . '%'; ?></td>
                        <td><?php echo round($row['strongly_agree'], 2) . '%'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
                <p>Email: <a href="mailto:your-email@example.com" class="text-light">your-email@example.com</a></p>
                <p><a href="https://facebook.com" class="text-light">Follow us on Facebook</a></p>
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

<?php
// Close the connection to the database
mysqli_close($conn);
?>
