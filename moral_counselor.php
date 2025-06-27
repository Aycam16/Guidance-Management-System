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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
    <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">GOOD MORAL</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="input-group ms-auto" style="max-width: 300px;">
        <input type="text" class="form-control" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-secondary" type="button">Search</button>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Student Number</th>
            <th>Course</th>
            <th>Section</th>
            <th>Enrollment Status</th>
            <th>Status</th>
            <th>Action</th> <!-- This is for View Details -->
            <th>Email Action</th> <!-- New header for the email button -->
        </tr>
    </thead>
    <tbody>
        <?php
        $appointments = [
            [
                "appointment_date" => "2024-12-14",
                "appointment_time" => "9:00 - 10:30 am",
                "full_name" => "Juan Dela Cruz",
                "email" => "juan_delacruz@gmail.com",
                "student_number" => "11-1111",
                "course" => "BSIT",
                "section" => "SBIT-2F",
                "reason" => "Bachelor Degree",
                "status" => "Upcoming"
            ],
            [
                "appointment_date" => "2024-12-15",
                "appointment_time" => "10:00 - 11:30 am",
                "full_name" => "Maria Clara",
                "email" => "maria_clara@gmail.com",
                "student_number" => "11-2222",
                "course" => "BSCS",
                "section" => "SBSC-3A",
                "reason" => "Senior High",
                "status" => "Completed"
            ]
        ];

        foreach ($appointments as $index => $appointment) {
            echo "<tr>";
            echo "<td>{$appointment['appointment_date']}</td>";
            echo "<td>{$appointment['appointment_time']}</td>";
            echo "<td>{$appointment['full_name']}</td>";
            echo "<td>{$appointment['email']}</td>";
            echo "<td>{$appointment['student_number']}</td>";
            echo "<td>{$appointment['course']}</td>";
            echo "<td>{$appointment['section']}</td>";
            echo "<td>{$appointment['reason']}</td>";
            echo "<td><span class='badge bg-success'>{$appointment['status']}</span></td>";
            echo "<td>
                    <button class='btn btn-primary btn-sm view-details-btn' 
                            data-index='{$index}' 
                            data-bs-toggle='modal' 
                            data-bs-target='#detailsModal'>View Details</button>
                  </td>";
            echo "<td>
                    <a href='mailto:{$appointment['email']}' class='btn btn-info btn-sm'>
                        <i class='fas fa-envelope'></i> Email
                    </a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>


    <!-- Pagination -->
    <div class="d-flex justify-content-between">
        <span>Showing 1-2 of 100</span>
        <nav>
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Appointment Date:</strong> <span id="modal-date"></span></p>
                <p><strong>Appointment Time:</strong> <span id="modal-time"></span></p>
                <p><strong>Full Name:</strong> <span id="modal-name"></span></p>
                <p><strong>Email:</strong> <span id="modal-email"></span></p>
                <p><strong>Student Number:</strong> <span id="modal-student-number"></span></p>
                <p><strong>Course:</strong> <span id="modal-course"></span></p>
                <p><strong>Section:</strong> <span id="modal-section"></span></p>
                <p><strong>Reason:</strong> <span id="modal-reason"></span></p>
                <p><strong>Status:</strong> <span id="modal-status"></span></p>
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
    <!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const appointments = <?php echo json_encode($appointments); ?>;
        const viewButtons = document.querySelectorAll(".view-details-btn");
        
        viewButtons.forEach(button => {
            button.addEventListener("click", function () {
                const index = this.getAttribute("data-index");
                const appointment = appointments[index];
                
                // Populate modal fields
                document.getElementById("modal-date").textContent = appointment.appointment_date;
                document.getElementById("modal-time").textContent = appointment.appointment_time;
                document.getElementById("modal-name").textContent = appointment.full_name;
                document.getElementById("modal-email").textContent = appointment.email;
                document.getElementById("modal-student-number").textContent = appointment.student_number;
                document.getElementById("modal-course").textContent = appointment.course;
                document.getElementById("modal-section").textContent = appointment.section;
                document.getElementById("modal-reason").textContent = appointment.reason;
                document.getElementById("modal-status").textContent = appointment.status;
            });
        });
    });
</script>
</body>
</html>
