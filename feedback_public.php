<?php
// Connect to the database
include 'database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form responses for each category
    $responses = [
        'accessibility' => $_POST['accessibility'],
        'professionalism' => $_POST['professionalism'],
        'timeliness' => $_POST['timeliness'],
        'effectiveness' => $_POST['effectiveness'],
        'confidentiality' => $_POST['confidentiality'],
    ];

    // Insert each feedback response into the database
    foreach ($responses as $category => $response) {
        // Create a statement string for the category
        $statement = "Placeholder statement for $category";

        // Prepare and execute the insertion query
        $stmt = $conn->prepare("INSERT INTO feedback (category, statement, response) VALUES (?, ?, ?)");
        
        // Bind parameters: $category is a string, $statement is a string, $response is an integer
        $stmt->bind_param('ssi', $category, $statement, $response);
        $stmt->execute();
    }

    // Success message after form submission
    echo "<p class='alert alert-success text-center'>Feedback submitted successfully!</p>";
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

        main {
            margin-bottom: 80px; /* Adjust value as needed */
        }

        body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            overflow-x: hidden; /* Prevent horizontal overflow */
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

        .table thead {
        background-color: #003366 !important; /* Background color for the table header */
        color: white; /* Ensure text is visible on dark background */
    }
    .table-bordered {
        border-color: #003366 !important; /* Table border color */
    }
    .table-bordered th, .table-bordered td {
        border-color: #003366 !important; /* Table cell border color */
    }
    .btn-primary {
        background-color: #003366 !important; /* Button background color */
        border-color: #003366 !important; /* Button border color */
    }
    .btn-primary:hover {
        background-color: #002244; /* Darker shade for hover effect */
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
                <li class="nav-item"><a class="nav-link" href="index_public.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">Services</a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="goodmoral.php">Good Moral Certificate</a>
                        <a class="dropdown-item" href="feedback_public.php">Share Your Feedback</a>
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

<main class="container mt-5">
        <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">Counseling Appointment Feedback</h1>
        <p class="text-center">We appreciate your feedback! Please take a moment to complete this survey about your experience with our Guidance and Counseling Services.</p>

        <form method="POST" action="feedback.php">
            <table class="table table-bordered text-center">
                <thead>
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
                    <tr>
                        <td>Accessibility of Services</td>
                        <td>The counseling office is easily accessible when I need support.</td>
                        <td><input type="radio" name="accessibility" value="1"></td>
                        <td><input type="radio" name="accessibility" value="2"></td>
                        <td><input type="radio" name="accessibility" value="3"></td>
                        <td><input type="radio" name="accessibility" value="4"></td>
                        <td><input type="radio" name="accessibility" value="5"></td>
                    </tr>
                    <tr>
                        <td>Professionalism of Staff</td>
                        <td>The guidance counselors are professional and courteous.</td>
                        <td><input type="radio" name="professionalism" value="1"></td>
                        <td><input type="radio" name="professionalism" value="2"></td>
                        <td><input type="radio" name="professionalism" value="3"></td>
                        <td><input type="radio" name="professionalism" value="4"></td>
                        <td><input type="radio" name="professionalism" value="5"></td>
                    </tr>
                    <tr>
                        <td>Timeliness of Response</td>
                        <td>My appointments and requests were handled in a timely manner.</td>
                        <td><input type="radio" name="timeliness" value="1"></td>
                        <td><input type="radio" name="timeliness" value="2"></td>
                        <td><input type="radio" name="timeliness" value="3"></td>
                        <td><input type="radio" name="timeliness" value="4"></td>
                        <td><input type="radio" name="timeliness" value="5"></td>
                    </tr>
                    <tr>
                        <td>Effectiveness of Counseling Sessions</td>
                        <td>The counseling sessions helped me address my concerns effectively.</td>
                        <td><input type="radio" name="effectiveness" value="1"></td>
                        <td><input type="radio" name="effectiveness" value="2"></td>
                        <td><input type="radio" name="effectiveness" value="3"></td>
                        <td><input type="radio" name="effectiveness" value="4"></td>
                        <td><input type="radio" name="effectiveness" value="5"></td>
                    </tr>
                    <tr>
                        <td>Confidentiality of Services</td>
                        <td>I feel confident that the counseling office maintains the confidentiality of my information.</td>
                        <td><input type="radio" name="confidentiality" value="1"></td>
                        <td><input type="radio" name="confidentiality" value="2"></td>
                        <td><input type="radio" name="confidentiality" value="3"></td>
                        <td><input type="radio" name="confidentiality" value="4"></td>
                        <td><input type="radio" name="confidentiality" value="5"></td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </div>
        </form>
    </main>


    <footer >
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="QCU_Logo.png" alt="Quirino University Logo" style="width: 80px; height: 80px; margin-right: 30px;">
                <div class="text-left">
                    <h5>Guidance and Counseling Unit</h5>
                    <p>Quirino Highway, San Bartolome, Novaliches, Quezon City <br>Mon - Fri 8am - 5pm</p>
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





    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>