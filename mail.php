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
        html,
        html, body {
                height: 100%;
                margin: 0;
                display: flex;
                flex-direction: column;
                font-family: Arial, sans-serif;
                box-sizing: border-box; /* Ensures padding and borders don't cause overflow */
            }

        body {
            background-color: #f5f5f5;
            display: grid;
            grid-template-rows: auto 1fr auto;
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        .email-interface {
            display: flex;
            height: calc(100vh - 100px);
            flex-grow: 1;
        }

        /* Sidebar */
        .email-sidebar {
            width: 30%;
            background-color: #fff;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .email-header {
            background-color: white;
            color: #004E98;
            padding: 15px;
            font-weight: bold;
            border-bottom: 2px solid #004E98;
            text-align: center;
        }

        .email-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            justify-content: space-around;
        }

        .email-tab {
            padding: 10px 0;
            text-align: center;
            width: 50%;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .email-tab.active {
            background-color: #004E98;
            color: white;
        }

        .email-list {
            list-style: none;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            height: 100%;
        }

        .email-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .email-item:hover {
            background-color: #f0f8ff;
        }

        .email-item.unread {
            font-weight: bold;
            background-color: #eef7ff;
        }

        .email-item .email-info {
            flex: 1;
        }

        .email-item .email-sender {
            font-size: 16px;
            font-weight: 600;
        }

        .email-item .email-subject {
            font-size: 14px;
            color: #555;
        }

        .email-item .email-timestamp {
            font-size: 12px;
            color: #999;
            white-space: nowrap;
        }

        /* Email Content */
        .email-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .email-view {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-y: auto;
        }

        .email-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .email-topbar h4 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #004E98;
        }

        .email-body p {
            margin-bottom: 15px;
            font-size: 14px;
            color: #333;
        }

        /* Email Actions */
        .email-actions {
            display: flex;
            justify-content: flex-start;
            gap: 15px;
            margin-top: 20px;
        }

        .email-actions .icon-action {
            font-size: 18px;
            cursor: pointer;
            color: #555;
            transition: color 0.3s;
        }

        .email-actions .icon-action:hover {
            color: #004E98;
        }

        .email-actions .fa-reply {
            color: #4CAF50;
        }

        .email-actions .fa-trash {
            color: #FF4C4C;
        }

        .email-actions .fa-archive {
            color: #6c757d;
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
                    <li class="nav-item position-relative">
                        <a href="#" class="text-white">
                            <i class="fas fa-envelope fa-lg"></i>
                            <span class="badge badge-danger position-absolute" style="top: -5px; right: -5px; font-size: 12px;">3</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">Profile</a>
                    </div>
                </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="email-interface">
        <div class="email-sidebar">
            <div class="email-header">Inbox</div>
            <div class="email-tabs">
                <div class="email-tab active" onclick="filterEmails('all')">All Mail</div>
                <div class="email-tab" onclick="filterEmails('unread')">Unread</div>
            </div>
            <ul class="email-list">
                <li class="email-item unread">
                    <div class="email-info">
                        <span class="email-sender">Guidance Unit</span>
                        <span class="email-subject">Re: Mental Health Survey</span>
                    </div>
                    <span class="email-timestamp">1 min ago</span>
                </li>
                <li class="email-item">
                    <div class="email-info">
                        <span class="email-sender">Guidance Unit</span>
                        <span class="email-subject">Follow-up: Your Recent Counseling</span>
                    </div>
                    <span class="email-timestamp">2 days ago</span>
                </li>
                <li class="email-item unread">
                    <div class="email-info">
                        <span class="email-sender">Guidance Unit</span>
                        <span class="email-subject">New Student Support Program</span>
                    </div>
                    <span class="email-timestamp">3 hours ago</span>
                </li>
            </ul>
        </div>

        <div class="email-content">
            <div class="email-view">
                <div class="email-topbar">
                    <h4>New Student Support Program</h4>
                    <div class="email-actions">
                        <span class="icon-action fa fa-reply"></span>
                        <span class="icon-action fa fa-trash"></span>
                        <span class="icon-action fa fa-archive"></span>
                    </div>
                </div>
                <div class="email-body">
                    <p>Dear Student,</p>
                    <p>We are excited to announce a new support program designed to help you succeed during your academic journey at QCU. Please click on the link below to get more information about the program.</p>
                    <p>Best Regards,</p>
                    <p>The QCU Guidance and Counseling Team</p>
                </div>
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


    <script>
        function filterEmails(type) {
            const emailItems = document.querySelectorAll('.email-item');
            emailItems.forEach(item => {
                if (type === 'unread') {
                    item.style.display = item.classList.contains('unread') ? 'block' : 'none';
                } else {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.email-tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
