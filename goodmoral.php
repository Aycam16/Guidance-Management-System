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

            .breadcrumb {
                background-color: #f8f9fa;
                padding: 10px 15px;
                margin-bottom: 15px;
                border-radius: 0.25rem;
            }

            #procedures {
                margin-bottom: 30px;
            }

            #formSection {
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease-in-out;
            }

            #formSection.show {
                display: block;
                opacity: 1;
            }

            .carousel-inner {
                min-height: 575px;
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


            button[type="submit"] {
                margin: 10px auto;
                display: block;
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

        #nextButton {
            background-color: #003366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            position: absolute; /* For positioning it to the right */
            right: 20px; /* Adjusts distance from the right edge */
            bottom: 60px; /* Adds space between the button and footer */
        }

        #nextButton:disabled {
            background-color: #666; /* Optional: Gray color for disabled state */
            cursor: not-allowed;
        }

        #nextButton:hover {
            background-color: #002244; /* Optional hover effect */
        }
        .custom-submit-btn {
        background-color: #004E98;
        border-color: #004E98;
        color: white; /* Make text visible */
        padding: 10px 20px; /* Add some padding for a standard button size */
        font-weight: bold;
        border-radius: 5px; /* Optional: rounded corners */
        }

        /* Center the button */
        .custom-submit-btn {
            display: block;
            margin: 20px auto; /* Auto centers horizontally */
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

        <main class="container mt-4">
            <!-- Breadcrumbs (only displayed in the form section) -->
            <nav aria-label="breadcrumb" id="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="goodmoral.php" >Good Moral Certificate</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Good Moral Certificate Request Form</li>
                </ol>
            </nav>

            <!-- Procedures Section -->
            <section id="procedures">
                <div class="bg-light p-4 rounded shadow-sm">
                <h3 class="text-center mb-4" style="color: #004E98; font-weight: bold;">REQUEST FOR GOOD MORAL</h3>
                    <h5 class="font-weight-bold mt-3">Procedures for Good Moral Certificates</h5>
                    <ol class="pl-3">
                        <li>Present one (1) copy of School Clearance and Identification Card to the Guidance and Counseling Unit staff.</li>
                        <li>Fill out all the information on the Good Moral Character Request Form.</li>
                        <li>The Guidance and Counseling Unit staff will immediately process the good moral request.</li>
                        <li>The Good Moral Character Certificate will be released on the day it is requested.</li>
                        <li>If the representative is the one claiming, the representative will be asked to present his/her identification card and the student's identification with an authorization letter.</li>
                    </ol>
                    <h5 class="font-weight-bold mt-3">For Online:</h5>
                    <ol class="pl-3">
                        <li>Click on the link for Requesting of Good Moral Character Certificate: 
                            <a href="javascript:void(0);" class="text-primary font-weight-bold" onclick="showForm()">Good Moral Certificate Request Form</a>
                        </li>
                        <li>Fill out all the information on the Good Moral Character Online Request Form.</li>
                        <li>The Guidance Staff will process the request and will contact the student about their claiming date through the official Guidance Facebook Page.</li>
                        <li>The student can request to claim the Good Moral Character Certificate via email or through a scheduled appointment.</li>
                    </ol>
                </div>
            </section>

        <!-- Request Form Section -->
        <section id="formSection" class="mt-4">
                <div class="bg-light p-4 rounded shadow-sm">
                    <h3 class="text-center mb-4" style="color: #004E98; font-weight: bold;">GOOD MORAL CERTIFICATE REQUEST FORM</h3>
                    <p class="font-italic">Reminder: Good moral requests can only be granted to individuals who are currently enrolled, were previously enrolled, or have graduated from this university.</p>
                    
                    <form id="mainForm" onsubmit="validateForm(event)">
                        <div class="form-group">
                            <label for="pickupLocation" class="font-weight-bold">PREFERRED PICK-UP LOCATION</label>
                            <select id="pickupLocation" class="form-control" required>
                            <option value="">What campus do you wish to claim your certificate?</option>
                                <option value="main">San Bartolome Campus (Main)</option>
                                <option value="batasan">Batasan Campus</option>
                                <option value="francisco">San Francisco Campus</option>
                            </select>
                        </div>
                        <!-- Enrollment Status -->
                        <div class="form-group">
                            <label class="font-weight-bold">ENROLLMENT STATUS</label>
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="enrollmentStatus" id="bachelor" value="bachelor" required>
                            <label class="form-check-label" for="bachelor">I am a BACHELOR Degree Student of this university</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="enrollmentStatus" id="senior" value="senior">
                            <label class="form-check-label" for="senior">I was a SENIOR HIGH SCHOOL Graduate/Undergraduate of this university</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="enrollmentStatus" id="graduate" value="graduate">
                            <label class="form-check-label" for="graduate">I am a GRADUATE of this university</label>
                        </div>
                        </div>

                        <!-- Data Privacy Consent -->
                        <label for="privacyConsent">
                        <input type="checkbox" id="privacyConsent" required>
                                Republic Act 10173 – Data Privacy Act of 2012 <br>
                                I give my consent to the collection and processing of my personal information by authorized personnel and officials connected to the University for legitimate purposes such as: conducting research for institutional development; and, all guidance and counseling-related functions. I also allow the University to use and release the information for the above stated purposes in accordance with the Data Privacy Act. Furthermore, all information I provided are true and accurate.
                            </label>
                        </div>

                        <div style="position: relative; min-height: 200px;"> <!-- Parent container -->
                    <button 
                        type="button" 
                        id="nextButton" 
                        class="btn" 
                        disabled 
                        onclick="showAdditionalForm()">
                        Next
                    </button>
                    </div>


                    </form>
                </div>
            </section>

            <!-- Bachelor Degree Additional Form Section (hidden by default if I click the next button) -->
                <section id="bachelorForm" class="mt-4" style="display: none;">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h3 class="text-center font-weight-bold mb-4">GOOD MORAL CERTIFICATE REQUEST FORM FOR DEGREE COURSES</h3>
                        <p class="font-italic">
                            Reminder: Good moral requests can only be granted to individuals who are currently enrolled, were previously enrolled, or have graduated from this university.
                        </p>

                        <form id="bachelorDegreeForm" onsubmit="submitBachelorForm(event)">
                            <!-- Name Section -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" class="form-control" placeholder="e.g., DELA CRUZ" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" class="form-control" placeholder="e.g., JUAN" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="middleInitial">Middle Initial</label>
                                    <input type="text" id="middleInitial" class="form-control" placeholder="e.g., G.">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="suffix">Suffix</label>
                                    <input type="text" id="suffix" class="form-control" placeholder="e.g., JR.">
                                </div>
                            </div>

                            <!-- Student Info Section -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="studentNumber">Student Number</label>
                                    <input type="text" id="studentNumber" class="form-control" placeholder="e.g., 11-2401" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="course">Course</label>
                                    <select id="course" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSBA">BSBA</option>
                                        <option value="BSE">BSE</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="schoolYearStart">What School Year did you start in QCU?</label>
                                    <input type="text" id="schoolYearStart" class="form-control" placeholder="e.g., 2019" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="semester">Semester</label>
                                    <select id="semester" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                    </select>
                                </div>
                                
                            </div>

                            <!-- Attendance Details -->
                            <div class="form-row">
                            <div class="form-group col-md-3">
                                    <label for="lastSchoolYear">Last School Year Attended</label>
                                    <select id="lastSchoolYear" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="semester">Semester</label>
                                    <select id="semester" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="graduateStatus">Are you already a graduate of QCU?</label>
                                    <select id="graduateStatus" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" placeholder="e.g., jdelacruz@gmail.com" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="purpose">What is the purpose of your Good Moral request?</label>
                                    <select id="purpose" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="employment">Employment</option>
                                        <option value="furtherStudies">Further Studies</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Remarks/Additional Request -->
                            <div class="form-group">
                                <label for="remarks">Any Remarks/Additional Request</label>
                                <textarea id="remarks" class="form-control" rows="3" placeholder="Enter any additional request or remarks here..."></textarea>
                            </div>

                            <!-- File Upload Section -->
                            <div class="form-group">
                                <label for="idUpload">Attach the scanned copy or picture of your QCU School ID, Registration Form, or any valid ID here:</label>
                                <input type="file" id="idUpload" class="form-control-file" accept="image/*" required>
                                <small class="form-text text-muted">Upload 1 supported file: Image. Max 100 MB.</small>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary custom-submit-btn">Submit</button>
                        </form>
                    </div>
                </section>

                <!-- SENIOR HIGH SCHOOL GRADUATES/UNDERGRADUATES Additional Form Section (hidden by default if I click the next button) -->
                <section id="seniorForm" class="mt-4" style="display: none;">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h3 class="text-center font-weight-bold mb-4">GOOD MORAL CERTIFICATE REQUEST FORM FOR SENIOR HIGH SCHOOL GRADUATES/UNDERGRADUATES</h3>
                        <p class="font-italic">
                            Reminder: Good moral requests can only be granted to individuals who are currently enrolled, were previously enrolled, or have graduated from this university.
                        </p>

                        <form id="seniorForm" onsubmit="submitSeniorForm(event)">
                            <!-- Name Section -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" class="form-control" placeholder="e.g., DELA CRUZ" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" class="form-control" placeholder="e.g., JUAN" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="middleInitial">Middle Initial</label>
                                    <input type="text" id="middleInitial" class="form-control" placeholder="e.g., G.">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="suffix">Suffix</label>
                                    <input type="text" id="suffix" class="form-control" placeholder="e.g., JR.">
                                </div>
                            </div>

                            <!-- Student Info Section -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="studentNumber">Student Number</label>
                                    <input type="text" id="studentNumber" class="form-control" placeholder="e.g., 11-2401" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="track/course">Track/Course</label>
                                    <select id="course" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="STEM">STEM</option>
                                        <option value="HUMSS">HUMSS</option>
                                        <option value="ABM">ABM</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="semester">Are you a Graduate of SHS in QCU?</label>
                                    <select id="semester" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="lastSchoolYear">Latest grade level in QCU</label>
                                    <select id="lastSchoolYear" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                                
                            </div>

                            <!-- Attendance Details -->
                            <div class="form-row">
                            
                                <div class="form-group col-md-3">
                                    <label for="semester">Semester</label>
                                    <select id="semester" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="graduateStatus">Last School Year Attended</label>
                                    <select id="graduateStatus" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" placeholder="e.g., jdelacruz@gmail.com" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="purpose">What is the purpose of your Good Moral request?</label>
                                    <select id="purpose" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="employment">Employment</option>
                                        <option value="furtherStudies">Further Studies</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>


                            <!-- Remarks/Additional Request -->
                            <div class="form-group">
                                <label for="remarks">Any Remarks/Additional Request</label>
                                <textarea id="remarks" class="form-control" rows="3" placeholder="Enter any additional request or remarks here..."></textarea>
                            </div>

                            <!-- File Upload Section -->
                            <div class="form-group">
                                <label for="idUpload">Attach the scanned copy or picture of your QCU School ID, Registration Form, or any valid ID here:</label>
                                <input type="file" id="idUpload" class="form-control-file" accept="image/*" required>
                                <small class="form-text text-muted">Upload 1 supported file: Image. Max 100 MB.</small>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary custom-submit-btn">Submit</button>
                        </form>
                    </div>
                </section>

                <!-- TECHVOC GRADUATES/UNDERGRADUATES Additional Form Section (hidden by default if I click the next button) -->
                <section id="graduateForm" class="mt-4" style="display: none;">
                    <div class="bg-light p-4 rounded shadow-sm">
                        <h3 class="text-center font-weight-bold mb-4">GOOD MORAL CERTIFICATE ONLINE REQUEST FORM FOR TECHNICAL VOCATIONAL COURSES</h3>
                        <p class="font-italic">
                            Reminder: Good moral requests can only be granted to individuals who are currently enrolled, were previously enrolled, or have graduated from this university.
                        </p>

                        <form id="graduateForm" onsubmit="submitSeniorForm(event)">
                            <!-- Name Section -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" class="form-control" placeholder="e.g., DELA CRUZ" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" class="form-control" placeholder="e.g., JUAN" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="middleInitial">Middle Initial</label>
                                    <input type="text" id="middleInitial" class="form-control" placeholder="e.g., G.">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="suffix">Suffix</label>
                                    <input type="text" id="suffix" class="form-control" placeholder="e.g., JR.">
                                </div>
                            </div>

                            <!-- Student Info Section -->
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="studentNumber">Student Number</label>
                                    <input type="text" id="studentNumber" class="form-control" placeholder="e.g., 11-2401" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="course">Course</label>
                                    <select id="course" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSBA">BSBA</option>
                                        <option value="BSE">BSE</option>
                                    </select>
                                </div>
                                
                            </div>

                            <!-- Attendance Details -->
                            <div class="form-row">
                            <div class="form-group col-md-3">
                                    <label for="lastSchoolYear">Last School Year Attended</label>
                                    <select id="lastSchoolYear" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="lastSchoolYear">Last School Year Attended</label>
                                    <select id="lastSchoolYear" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="2024">2024</option>
                                        <option value="2023">2023</option>
                                        <option value="2022">2022</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                            <div class="form-group col-md-3">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control" placeholder="e.g., jdelacruz@gmail.com" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="purpose">What is the purpose of your Good Moral request?</label>
                                    <select id="purpose" class="form-control" required>
                                        <option value="">Choose...</option>
                                        <option value="employment">Employment</option>
                                        <option value="furtherStudies">Further Studies</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Remarks/Additional Request -->
                            <div class="form-group">
                                <label for="remarks">Any Remarks/Additional Request</label>
                                <textarea id="remarks" class="form-control" rows="3" placeholder="Enter any additional request or remarks here..."></textarea>
                            </div>

                            <!-- File Upload Section -->
                            <div class="form-group">
                                <label for="idUpload">Attach the scanned copy or picture of your QCU School ID, Registration Form, or any valid ID here:</label>
                                <input type="file" id="idUpload" class="form-control-file" accept="image/*" required>
                                <small class="form-text text-muted">Upload 1 supported file: Image. Max 100 MB.</small>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary custom-submit-btn">Submit</button>

                        </form>
                    </div>
                </section>
        </main>

        <footer>
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

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
        function showForm() {
        document.getElementById('procedures').style.display = 'none';
        document.getElementById('formSection').classList.add('show');
    }

   // Function to handle enabling/disabling of the 'Next' button
    function toggleNextButton() {
        const bachelorRadio = document.getElementById('bachelor');
        const seniorRadio = document.getElementById('senior');
        const graduateRadio = document.getElementById('graduate');
        const privacyConsent = document.getElementById('privacyConsent');
        const nextButton = document.getElementById('nextButton');

        // The button is enabled only if a radio button is selected and privacy consent is given
        nextButton.disabled = !( (bachelorRadio.checked || seniorRadio.checked || graduateRadio.checked) && privacyConsent.checked );
    }

    // Listen for changes to the radio buttons and privacy consent checkbox
    document.getElementById('bachelor').addEventListener('change', toggleNextButton);
    document.getElementById('senior').addEventListener('change', toggleNextButton);
    document.getElementById('graduate').addEventListener('change', toggleNextButton);
    document.getElementById('privacyConsent').addEventListener('change', toggleNextButton);

    // Function to show the additional form after clicking "Next"
    function showAdditionalForm() {
        // Assuming the 'Next' button was clicked, show the next form section
        const bachelorForm = document.getElementById('bachelorForm');
        const seniorForm = document.getElementById('seniorForm');
        const graduateForm = document.getElementById('graduateForm');
        
        const formSection = document.getElementById('formSection');
        const additionalForm = document.getElementById('additionalForm');
        
        // Hide the initial form section
        formSection.style.display = 'none';

        // Show the relevant additional form based on the selected radio button
        if (document.getElementById('bachelor').checked) {
            bachelorForm.style.display = 'block';
        } else if (document.getElementById('senior').checked) {
            seniorForm.style.display = 'block';
        } else if (document.getElementById('graduate').checked) {
            graduateForm.style.display = 'block';
        }

        // Show the additional form section
        additionalForm.style.display = 'block';
    }

    // Call toggleNextButton when the page loads to ensure initial state is correct
    window.onload = toggleNextButton;
    </script>

    </body>
    </html>
