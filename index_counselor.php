<?php
session_start();
require 'database.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'counselor') {
    header('Location: index_public.php');
    exit();
}

// Fetch top 3 most important announcements
$sql = "SELECT title, description, post_date, image_path, posted_by 
        FROM announcement 
        WHERE is_removed = 0 AND is_archived = 0 AND is_important = 1 
        ORDER BY post_date DESC 
        LIMIT 3";
$result = $conn->query($sql);

$announcements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Stay updated with the latest announcements from QCU Guidance and Counseling.">
    <title>QCU Guidance and Counseling</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
            box-sizing: border-box; /* Ensures padding and borders don't cause overflow */
        }

        *,
        *::before,
        *::after {
            box-sizing: inherit;
        }

        body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
            width: 100%; /* Ensure container does not overflow */
            overflow-x: hidden; /* Prevent horizontal overflow */
        }

        .jumbotron {
            height: 575px;
            background-size: cover;
            background-position: center;
            width: 100%;
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

        .card {
            width: 100%;
            max-width: 550px;
            border-radius: 30px;
            margin: 15px;
            overflow: hidden;
            background-color: #C5DDF3;
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

        .btn-warning:hover {
            background-color: #FFC107;
            color: black;
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

        .navbar {
            padding: 0.5rem 1rem;
        }

        .modal-content {
            max-width: 100%;
            margin: 0;
        }

        .btn-custom {
            background-color: #EFF4F9;
            color: #004E98;
            border: 2px solid #004E98;
            font-weight: bold;
            padding: 15px;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #004E98;
            color: #EFF4F9;
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
    <main>
    <div id="content">
        <!-- This is where the loaded content will appear -->
    
        <!-- Jumbotron Section -->
        <div id="imageCarousel" class="carousel slide text-center text-dark" data-ride="carousel" data-interval="8000" style="height: 575px;">
            <div class="carousel-inner" style="height: 575px;">
                <div class="carousel-item active" style="background-image: url('profile.png'); height: 575px;">
                    <h1 class="display-4"></h1>
                    <p class="lead"></p>
                </div>
            </div>
        </div>

              <!-- Announcement Section -->
              <div class="container-fluid mt-5">
    <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">ANNOUNCEMENTS</h1>
    <div id="announcementsCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($announcements as $index => $announcement): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="card text-center mx-auto" style="width: 400px; border-radius: 20px; background-color: #C5DDF3;">
                        <img src="uploads/<?= htmlspecialchars($announcement['image_path'] ?: 'default_image.jpg') ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($announcement['title']) ?>" 
                             style="border-radius: 15px; height: auto;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($announcement['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($announcement['description']) ?></p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal" 
                                    data-title="<?= htmlspecialchars($announcement['title']) ?>" 
                                    data-description="<?= htmlspecialchars($announcement['description']) ?>" 
                                    data-date="<?= htmlspecialchars($announcement['post_date']) ?>" 
                                    data-location="<?= htmlspecialchars($announcement['location'] ?? 'N/A') ?>" 
                                    data-organizer="<?= htmlspecialchars($announcement['organizer'] ?? 'N/A') ?>" 
                                    data-image="uploads/<?= htmlspecialchars($announcement['image_path']) ?>">
                                Read More
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Carousel Controls -->
        <a class="carousel-control-prev" href="#announcementsCarousel" role="button" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: #004E98;"></span>
        </a>
        <a class="carousel-control-next" href="#announcementsCarousel" role="button" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: #004E98;"></span>
        </a>
    </div>
    <div class="text-center mt-4 mb-5"> <!-- Added mb-5 for spacing -->
    <a href="announcement_guidance.php" class="btn btn-lg" style="background-color: #004E98; color: white;">SEE ALL</a>
</div>
</div>




<!-- Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body d-flex align-items-center">
                <!-- Left Side: Image -->
                <div class="col-md-5 p-3">
                    <img id="modalImage" src="" alt="Announcement Image" class="img-fluid rounded" style="width: 100%; height: auto;">
                </div>
                <!-- Right Side: Content -->
                <div class="col-md-7 p-3">
                    <h5 id="announcementModalLabel" class="modal-title mb-3"></h5>
                    <p id="modalDescription"></p>
                    <hr>
                    <p><strong>Date:</strong> <span id="modalDate"></span></p>
                    <p><strong>Posted By:</strong> <span id="modalPostedBy"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
           

     </main> 

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
    <script>
    // Fill the modal with the announcement details when the "Read More" button is clicked
    document.addEventListener('DOMContentLoaded', () => {
    const announcementModal = document.getElementById('announcementModal');
    announcementModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;

        // Fetch attributes from button
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const date = button.getAttribute('data-date');
        const postedBy = button.getAttribute('data-posted-by');
        const image = button.getAttribute('data-image');

        // Populate modal fields
        announcementModal.querySelector('#announcementModalLabel').textContent = title;
        announcementModal.querySelector('#modalDescription').textContent = description;
        announcementModal.querySelector('#modalDate').textContent = date;
        announcementModal.querySelector('#modalPostedBy').textContent = postedBy;
        announcementModal.querySelector('#modalImage').src = image;
    });
});

</script>



</body>
</html>