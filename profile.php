<?php
// Connect to the database
session_start();
require_once 'database.php'; // Assuming you have a db_connection.php file to manage DB connection

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

$conn->close();
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
        margin-bottom: 110px; /* Add space to prevent overlapping of content with footer */
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

        .profile-container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        /* Profile image styling */
        .profile-header img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            border: 3px solid #004E98; /* Blue border around the profile picture */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        /* Hover effect for the profile image */
        .profile-header img:hover {
            transform: scale(1.1); /* Slight zoom on hover */
            box-shadow: 0 4px 15px rgba(0, 78, 152, 0.6); /* Shadow effect on hover */
        }

        .profile-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin-top: 10px;
        }

        .profile-header p {
            font-size: 16px;
            color: #777;
        }

        .profile-header .edit-btn {
            padding: 8px 16px;
            background-color: #004E98;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .profile-header .edit-btn:hover {
            background-color: #003366;
        }

        /* Style the "Change Profile Picture" input */
        .profile-header label {
            font-weight: 600;
            color: #004E98;
            font-size: 16px;
        }

        .profile-header input[type="file"] {
            display: none; /* Hide the default file input */
        }

        /* Custom file input button */
        .profile-header .custom-file-upload {
            display: inline-block;
            background-color: #004E98;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .profile-header .custom-file-upload:hover {
            background-color: #003366;
        }

        /* Add a preview box for the image upload */
        .image-preview {
            width: 120px;
            height: 120px;
            background-color: #f4f4f4;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid #ddd;
            overflow: hidden;
            margin-top: 15px;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .profile-info input,
        .profile-info select,
        .profile-info textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f8f8f8;
        }

        .profile-info label {
            font-size: 14px;
            font-weight: 600;
        }

        .profile-info button {
            width: 100%;
            padding: 12px;
            background-color: #004E98;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .profile-info button:hover {
            background-color: #003366;
        }

        @media (max-width: 768px) {
            .profile-info {
                grid-template-columns: 1fr;
            }
        }

        /* Modal Styling */
        .modal-content {
            padding: 20px;
        }

        .modal-body img {
            width: 100%;
            height: auto;
            border-radius: 10px;
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

    <div class="container">
    <div class="profile-container">
        <div class="profile-header">
            <div>
                <!-- Profile image with a placeholder -->
                <img id="profile-image" src="userpic.png" alt="Profile Image" />
                <h2>John Doe</h2>
                <p id="profile-email">john.doe@gmail.com</p>
            </div>
            <div>
                <button class="edit-btn">Edit</button>
            </div>
        </div>

        <form>
            <div class="profile-info">
                <div>
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" value="Doe" disabled>
                </div>
                <div>
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" value="John" disabled>
                </div>
                <div>
                    <label for="middle-name">Middle Name</label>
                    <input type="text" id="middle-name" value="Michael" disabled>
                </div>
                <div>
                    <label for="suffix">Suffix</label>
                    <input type="text" id="suffix" value="Jr." disabled>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" value="john.doe@gmail.com" disabled>
                </div>
                <div>
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" value="1990-04-15" disabled>
                </div>
                <div>
                    <label for="gender">Gender</label>
                    <select id="gender" disabled>
                        <option value="male" selected>Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label for="course">Course</label>
                    <select id="course" disabled>
                        <option value="cs" selected>Computer Science</option>
                        <option value="it">Information Technology</option>
                        <option value="se">Software Engineering</option>
                    </select>
                </div>
                <div>
                    <label for="year-level">Year Level</label>
                    <select id="year-level" disabled>
                        <option value="1st" selected>1st Year</option>
                        <option value="2nd">2nd Year</option>
                        <option value="3rd">3rd Year</option>
                        <option value="4th">4th Year</option>
                    </select>
                </div>
                <div>
                    <label for="status">Status</label>
                    <select id="status" disabled>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Profile Picture -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalProfilePic" src="default-profile.jpg" alt="Profile Picture">
                <input type="file" id="file-upload" accept="image/*" onchange="previewImage(event)">
                <label for="file-upload" class="custom-file-upload">Choose a New Image</label>
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
                <address>
                    Quirino Highway, San Bartolome, Novaliches, Quezon City<br>Mon - Fri 8am - 5pm
                </address>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Get the Edit button and the form inputs
    const editButton = document.querySelector('.edit-btn');
    const formElements = document.querySelectorAll('.profile-info input, .profile-info select');
    const emailField = document.getElementById('email');
    const profileEmail = document.getElementById('profile-email'); // Profile email element

    // JavaScript for handling profile image upload and preview
    const imageUploadInput = document.getElementById('file-upload');
    const profileImage = document.getElementById('profile-image');
    const modalProfilePic = document.getElementById('modalProfilePic'); // Get the modal profile picture
    const profileModal = new bootstrap.Modal(document.getElementById('profileModal')); // Initialize the modal

    // When the profile image is clicked, show the modal
    profileImage.addEventListener('click', function () {
        profileModal.show(); // Show the modal
    });

    // When the Edit button is clicked
    editButton.addEventListener('click', function () {
        // Toggle the disabled state of form fields
        formElements.forEach(element => {
            if (element.disabled) {
                element.disabled = false; // Enable the field
                editButton.textContent = "Save Changes"; // Change button text to "Save Changes"
            } else {
                element.disabled = true; // Disable the field
                editButton.textContent = "Edit"; // Change button text back to "Edit"
                
                // Update the profile email text when saved
                if (emailField.value !== profileEmail.textContent) {
                    profileEmail.textContent = emailField.value; // Update the email in profile header
                }
            }
        });
    });

    // JavaScript for handling profile image upload and preview
    imageUploadInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            // Create a FileReader object to read the file
            const reader = new FileReader();

            reader.onload = function (e) {
                // Update both the profile picture and modal image preview
                profileImage.src = e.target.result;
                modalProfilePic.src = e.target.result;
            };

            reader.readAsDataURL(file);
        }
    });
});

</script>

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
