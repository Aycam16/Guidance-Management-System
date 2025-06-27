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


// Fetch the counselor's name if logged in
if (isset($_SESSION['employee_ID'])) {
    $employeeID = $_SESSION['employee_ID'];
    $stmt = $conn->prepare("SELECT first_name, last_name FROM counselors WHERE employee_ID = ?");
    $stmt->bind_param("s", $employeeID);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName);
    $stmt->fetch();
    $stmt->close();
    $postedBy = $firstName . ' ' . $lastName;
} else {
    $postedBy = 'Unknown Counselor';
}

// Create a new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_announcement'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] == 0) {
        $image = uploadImage($_FILES['announcement_image']);
    }

    // Use a default image if none was uploaded
    $image = $image ?: 'default_image.png';

    // Insert announcement into the database
    $stmt = $conn->prepare("
        INSERT INTO announcement (title, description, image_path, posted_by, post_date, edited_at, is_archived, is_removed) 
        VALUES (?, ?, ?, ?, NOW(), NOW(), 0, 0)
    ");
    $stmt->bind_param("ssss", $title, $description, $image, $postedBy);
    $stmt->execute();
    $stmt->close();

    header("Location: announcement_guidance.php");
    exit();
}

// Soft delete an announcement
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("UPDATE announcement SET is_removed = 1 WHERE Announcement_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: announcement_guidance.php");
    exit();
}

// Fetch all active announcements
$result = $conn->query("
    SELECT * FROM announcement 
    WHERE is_archived = 0 AND is_removed = 0 
    ORDER BY post_date DESC
");

// Upload image function
function uploadImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $uploadDir = 'uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return null;
    }

    $fileName = uniqid('announcement_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $fileName;
    } else {
        error_log("Failed to move uploaded file to $uploadPath.");
        return null;
    }
}

$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $searchQuery = "%{$searchTerm}%";
    $stmt = $conn->prepare("
        SELECT * FROM announcement 
        WHERE (title LIKE ? OR description LIKE ?) 
        AND is_archived = 0 AND is_removed = 0 
        ORDER BY post_date DESC
    ");
    $stmt->bind_param("ss", $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query("
        SELECT * FROM announcement 
        WHERE is_archived = 0 AND is_removed = 0 
        ORDER BY post_date DESC
    ");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_announcement'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $isImportant = isset($_POST['is_important']) ? 1 : 0;

    $image = '';
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] == 0) {
        $image = uploadImage($_FILES['announcement_image']);
    }

    // Use a default image if none was uploaded
    $image = $image ?: 'default_image.png';

    $stmt = $conn->prepare("INSERT INTO announcement (title, description, image_path, posted_by, post_date, is_archived, is_removed, is_important) 
                            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, 0, 0, ?)");
    $stmt->bind_param("ssssi", $title, $description, $image, $postedBy, $isImportant);
    $stmt->execute();
    $stmt->close();
    header("Location: announcement_guidance.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_announcement'])) {
    $announcementId = $_POST['announcement_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $isImportant = isset($_POST['is_important']) ? 1 : 0;

    // Handle image upload (optional)
    $image = '';
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] == 0) {
        $image = uploadImage($_FILES['announcement_image']);
    }

    // Update query
    $query = "UPDATE announcement SET title = ?, description = ?, status = ?, is_important = ?, edited_at = NOW()";
    $params = [$title, $description, $status, $isImportant];

    if ($image) {
        $query .= ", image_path = ?";
        $params[] = $image;
    }
    $query .= " WHERE Announcement_ID = ?";
    $params[] = $announcementId;

    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $stmt->close();

    header("Location: announcement_guidance.php");
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
            bottom: 0;
            background-color: #003366;
            width: 100%;
            color: white;
            padding: 20px;
        }

        footer.text-center {
            background-color: #004E98;
            color: white;
            padding: 10px;
        }

        .announcement-table {
            border-collapse: separate;
            border-spacing: 0 10px;
            background-color: #C5DDF3;
            border-radius: 20px;
            width: 100%;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .announcement-table th {
            background-color: #004E98;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: none;
        }

        .announcement-table td {
            padding: 10px;
            border: none;
            background-color: #ffffff;
            border-radius: 15px;
        }

        .announcement-table tr:hover td {
            background-color: #e6f0fa;
        }

        .table-actions i {
            cursor: pointer;
            margin-right: 10px;
        }

        .status-posted {
            color: green;
            font-weight: bold;
        }

         /* Add Announcement Button */
    .btn-primary {
        background-color: #004E98;
        border-color: #004E98;
    }

    .btn-primary:hover {
        background-color: #003366; /* Darker shade for hover effect */
        border-color: #003366;
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

    .search-input {
        max-width: 300px;
        width: 100%;
    }

    .announcement-table {
        margin-bottom: 30px; /* Adjust this value as needed */
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
                    <li class="nav-item"><a class="nav-link" href="mail.php"><i class="fas fa-envelope"></i></a></li>
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
        <h1 class="text-center">Announcements Management</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">+ Create Announcement</button>

        <div class="search-bar mb-3 d-flex justify-content-end">
    <form action="announcement_guidance.php" method="GET" class="d-flex">
        <input type="text" name="search" class="form-control me-2" 
               placeholder="Search announcements..." 
               value="<?= htmlspecialchars($searchTerm) ?>" style="max-width: 300px;">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

  
<div class="container">
<table class="announcement-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Posted By</th>
            <th>Date Posted</th>
            <th>Last Edited</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['posted_by']) ?></td>
                <td><?= htmlspecialchars($row['post_date']) ?></td>
                <td><?= htmlspecialchars($row['edited_at']) ?></td>
                <td>
                    <!-- Edit Button triggers Modal -->
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                            data-bs-target="#editAnnouncementModal" 
                            data-id="<?= $row['Announcement_ID'] ?>" 
                            data-title="<?= htmlspecialchars($row['title']) ?>" 
                            data-description="<?= htmlspecialchars($row['description']) ?>">
                        Edit
                    </button>
                    <a href="announcement_guidance.php?delete_id=<?= $row['Announcement_ID'] ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to delete this announcement?');">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">No announcements found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
    </div>
    </div>

    <!-- Add Announcement Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="announcement_guidance.php" method="POST" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="announcement_image" class="form-label">Image</label>
                            <input type="file" name="announcement_image" id="announcement_image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="posted_by" class="form-label">Posted By</label>
                            <input type="text" name="posted_by" id="posted_by" class="form-control" value="<?= htmlspecialchars($postedBy) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="Posted">Posted</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_important" id="is_important" class="form-check-input">
                            <label for="is_important" class="form-check-label">Mark as Important</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="create_announcement" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="announcement_guidance.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="announcement_id" id="edit_announcement_id">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_announcement_image" class="form-label">Image</label>
                        <input type="file" name="announcement_image" id="edit_announcement_image" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="Posted">Posted</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_important" id="edit_is_important" class="form-check-input">
                        <label for="edit_is_important" class="form-check-label">Mark as Important</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_announcement" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
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
     <!-- FontAwesome JS -->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
     <script>
    document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editAnnouncementModal');
    editModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const announcementId = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const status = button.getAttribute('data-status');
        const isImportant = button.getAttribute('data-is-important') === '1';

        editModal.querySelector('#edit_announcement_id').value = announcementId;
        editModal.querySelector('#edit_title').value = title;
        editModal.querySelector('#edit_description').value = description;
        editModal.querySelector('#edit_status').value = status;
        editModal.querySelector('#edit_is_important').checked = isImportant;
    });
});

</script>


    </body>
</html>