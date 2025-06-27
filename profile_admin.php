<?php
// Start the session at the beginning of the script
session_start();

// Include your database connection
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index_public.php');
    exit();
}

// Ensure the user has the 'counselor' role
if ($_SESSION['role'] !== 'admin') {
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

// Fetch the admin info from the database
$query = "SELECT * FROM admin_info WHERE Employee_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the admin info (name, position, email)
    if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['position'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $name = $first_name . ' ' . $last_name; // Combine first and last name
        $email = trim($_POST['email']);
        $position = trim($_POST['position']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format.";
        } else {
            $updateQuery = "UPDATE admin_info SET name = ?, email = ?, position = ? WHERE Employee_ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('sssi', $name, $email, $position, $admin_id);
            if ($updateStmt->execute()) {
                $success_message = "Profile updated successfully.";
            } else {
                $error_message = "Error updating profile.";
            }
        }
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = $_FILES['profile_picture'];
        $upload_dir = 'uploads/';
        $filename = time() . '_' . basename($profile_picture['name']);
        $target_path = $upload_dir . $filename;

        // Validate image file type (accepts only jpg, jpeg, png, gif)
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $valid_extensions)) {
            $error_message = "Invalid file type. Only JPG, JPEG, PNG, GIF files are allowed.";
        } else {
            // Move the uploaded file to the server
            if (move_uploaded_file($profile_picture['tmp_name'], $target_path)) {
                // Update the profile picture in the database
                $updatePicQuery = "UPDATE admin_info SET profile_pic = ? WHERE Employee_ID = ?";
                $updatePicStmt = $conn->prepare($updatePicQuery);
                $updatePicStmt->bind_param('si', $filename, $admin_id);
                if ($updatePicStmt->execute()) {
                    $success_message = "Profile picture updated successfully.";
                } else {
                    $error_message = "Error updating profile picture.";
                }
            } else {
                $error_message = "Error uploading file.";
            }
        }
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
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom styling */
 /* Reset margin and padding on body and html */
 html, body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        height: 100%;
        background-color: #f8f9fa;
    }

    /* Container Styling */
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding-top: 20px;
    }

    .profile-container {
        background-color: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
    }

    /* Profile Header */
    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
    }

    .profile-header img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-header div h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .profile-header div p {
        margin: 5px 0;
        color: #6c757d;
    }

    .edit-btn {
        background-color: #004E98;
        color: white;
        border: none;
        padding: 10px 18px;
        cursor: pointer;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .edit-btn:hover {
        background-color: #005b98;
    }

    /* Success/Error Messages */
    .alert {
        margin-top: 20px;
        padding: 15px;
        border-radius: 6px;
        font-size: 1rem;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Form Styling */
    form div {
        margin-bottom: 18px;
    }

    form label {
        font-weight: bold;
        font-size: 1.1rem;
        display: block;
        margin-bottom: 5px;
    }

    form input {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 1rem;
        box-sizing: border-box;
    }

    form input:focus {
        border-color: #004E98;
        outline: none;
    }

    form button {
        width: 100%;
        padding: 12px;
        background-color: #004E98;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    form button:hover {
        background-color: #005b98;
    }

    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg" style="background-color: #004E98; color: white;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#" aria-label="Admin Dashboard">
                <i class="fas fa-shield-alt" aria-hidden="true"></i> Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-label="Profile Options">
                            <i class="fas fa-user"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="profile_admin.php" aria-label="Go to Profile">Profile</a>
                            <a class="dropdown-item" href="?logout=true" aria-label="Logout">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container">
    <div class="profile-container">
        <div class="profile-header">
            <div>
                <img id="profile-image" src="uploads/<?= htmlspecialchars($admin['profile_pic'] ?? 'default-profile.jpg') ?>" alt="Profile Image">
                <h2 id="profile-name"><?= htmlspecialchars($admin['name'] ?? 'No Name Available') ?></h2>
                <p id="profile-email"><?= htmlspecialchars($admin['email'] ?? 'No Email Available') ?></p>
                <p id="profile-position"><?= htmlspecialchars($admin['position'] ?? 'No Position Available') ?></p>
            </div>
            <div>
                <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#profileModal">Change Profile Picture</button>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Form to edit profile -->
        <form method="POST">
            <div>
                <label for="first_name">Name</label>
                <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars(explode(' ', $admin['name'])[0] ?? '') ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
            </div>
            <div>
                <label for="position">Position</label>
                <input type="text" name="position" id="position" value="<?= htmlspecialchars($admin['position'] ?? '') ?>" required>
            </div>
            <button type="submit" class="edit-btn">Save Changes</button>
        </form>
    </div>
</div>

<!-- Profile Picture Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                <label for="profile_picture" class="custom-file-upload">Choose a file</label>
                <br>
                <img id="modalProfilePic" src="uploads/<?= htmlspecialchars($admin['profile_pic'] ?? 'default-profile.jpg') ?>" alt="Profile Preview" style="width: 100px; height: 100px; border-radius: 50%; margin-top: 10px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="uploadForm">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Handle profile picture preview
    document.getElementById('profile_picture').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modalProfilePic').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>



