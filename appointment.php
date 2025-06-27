<?php
session_start();
require_once 'database.php'; // Adjust to your DB connection file

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index_public.php');
    exit();
}

// Ensure the user has the 'student' role
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

// Handle appointment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_appointment'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $section = $conn->real_escape_string($_POST['section']);
    $course = $conn->real_escape_string($_POST['course']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);

    // Check if the time slot is already taken for the selected date
    $check_sql = "SELECT * FROM appointments WHERE appointment_date = '$date' AND appointment_time = '$time' AND status != 'Cancelled'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $message = "This time slot is already taken. Please choose a different time.";
    } else {
        // Insert appointment into the database
        $sql = "INSERT INTO appointments (name, email, section, course, reason, appointment_date, appointment_time, status) 
                VALUES ('$name', '$email', '$section', '$course', '$reason', '$date', '$time', 'Pending')";
                
        if ($conn->query($sql) === TRUE) {
            // Get the appointment_id of the newly inserted record
            $appointment_id = $conn->insert_id;
            
            // Insert a record into the appointment_history table
            $history_sql = "INSERT INTO appointment_history (appointment_id, status, updated_at) 
                            VALUES ('$appointment_id', 'Pending', NOW())";
            if ($conn->query($history_sql) === TRUE) {
                $message = "Appointment booked successfully and history recorded!";
            } else {
                $message = "Error inserting into appointment history: " . $conn->error;
            }
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$appointments_sql = "SELECT a.appointment_id, a.name, a.appointment_date, a.appointment_time, h.status, h.updated_at 
                     FROM appointments a 
                     LEFT JOIN appointment_history h ON a.appointment_id = h.appointment_id 
                     WHERE a.email = ? 
                     ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($appointments_sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$appointments_result = $stmt->get_result();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCU Guidance and Counseling | Appointment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

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
            display: flex;
            flex-direction: column;
        }

        main {
            margin-bottom: 2rem; /* Add space between content and footer */
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        table {
            margin-left: auto;
            margin-right: auto;
        }

        /* Main container for the page */
        .appointment-container {
            background-color: #e0ebf7;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
            flex-grow: 1;
            margin-top: 2rem; /* Add space at the top for better styling */
        }

        .col-lg-8,
        .col-lg-3 {
            margin: 0 auto; /* Centers the columns individually */
        }

        .calendar {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: stretch;
            height: calc(100vh - 200px);
        }

        .form-container {
            background-color: #f1f7ff;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            min-width: 45%;
        }

        #calendar {
            width: 100%;
            height: 100% !important;
            background-color: #f1f7ff;
            border: none;
            border-radius: 10px;
            padding: 15px;
        }

        .fc-toolbar {
            background-color: #f1f7ff;
            border: none;
        }

        .fc-title {
            font-weight: bold;
            color: #004E98;
        }

        .fc-button {
            background-color: #004E98;
            color: white;
            border-radius: 5px;
            padding: 10px;
        }

        .fc-button:hover {
            background-color: #00396b;
        }

        .fc-daygrid-day-selected {
            background-color: #004E98 !important;
            color: white !important;
        }

        .fc-daygrid-day-number {
            font-size: 1.2rem;
        }

        /* Styling the Confirm Appointment button */
        .btn-confirm {
            background-color: #6c757d;
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            cursor: not-allowed;
        }

        .btn-confirm.enabled {
            background-color: rgba(0, 51, 102, 1);
            cursor: pointer;
        }

        .btn-confirm:hover {
            background-color: #565e64;
            transition: background-color 0.3s ease;
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

        h1 {
            color: #004E98;
            text-align: center;
            margin-top: 1.5rem;
        }

        .btn-primary:hover {
            background-color: #003366; /* Darker shade on hover */
            color: white; /* Text color on hover */
        }

        .btn-warning:hover {
            background-color: #FFC107; /* Change background on hover */
            color: black; /* Text color on hover */
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
    <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">COUNSELING APPOINTMENT</h1>
    <div id="content" class="appointment-container">
        <div class="col-lg-8 bg-white p-3 rounded shadow-sm">
            <!-- Calendar Section -->
            <div class="calendar mb-4">
                <div id="calendar"></div>
            </div>

            <!-- Form Section -->
            <div class="form-container">
                <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
                <form id="appointment-form" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="e.g. John G. Doe" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="e.g. johndoe@example.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="section">Section</label>
                                <input type="text" class="form-control" id="section" name="section" placeholder="e.g. SBIT2F" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course">Choose Course</label>
                                <select class="form-control" id="course" name="course" required>
                                    <option value="">Choose</option>
                                    <option value="BSIT">BSIT</option>
                                    <option value="BSCS">BSCS</option>
                                    <option value="BSIS">BSIS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <select class="form-control" id="reason" name="reason" required>
                            <option value="">Choose</option>
                            <option value="Mental">Mental</option>
                            <option value="School">School</option>
                            <option value="Relationship">Relationship</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Choose Appointment Date</label>
                                <input type="text" class="form-control" id="date" name="date" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time">Choose Time Slot</label>
                                <select class="form-control" id="time" name="time" required>
                                    <option value="">Choose</option>
                                    <option value="8:00 - 9:00 am">8:00 - 9:00 am</option>
                                    <option value="9:00 - 10:00 am">9:00 - 10:00 am</option>
                                    <!-- Add more time slots as needed -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3" name="submit_appointment">Confirm Appointment</button>
                </form>
            </div>
        </div>

        <!-- Appointment History -->
        <div class="col-lg-3 appointment-history">
            <h4>Appointment History</h4>
            <table class="table table-bordered">
    <thead>
        <tr>
            <th>Appointment ID</th>
            <th>Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($appointments_result->num_rows > 0): ?>
            <?php while ($row = $appointments_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['appointment_id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['updated_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No appointment history found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

        </div>
    </div>
</main>


    <!-- Main Footer -->
<footer>
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="QCU_Logo.png" alt="Quirino University Logo" style="width: 80px; height: 80px; margin-right: 30px;">
                <div class="text-left">
                    <h5>Guidance and Counseling Unit</h5>
                    <p>Quirino Highway, San Bartolome, Novaliches, Quezon City <br>Mon - Fri 8am - 5pm</p>
                </div>
            </div>
            <div class="text-left">
            <p>Email: <a href="mailto:guidance.unit@qcu.edu.ph" class="text-light">guidance.unit@qcu.edu.ph</a></p>
            <p><a href="https://www.facebook.com/qcuguidanceunit" class="text-light">Follow us on Facebook</a></p>
            </div>
        </div>
    </footer>

    <!-- Copyright Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; 2024 QCU Guidance and Counseling</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    
    <script>
// Initialize FullCalendar
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        dateClick: function(info) {
            // On date click, set the selected date
            document.getElementById('date').value = info.dateStr;
            // Enable the time selection
            document.getElementById('time').disabled = false;
        }
    });
    calendar.render();
});
</script>
</body>
</html>
