<?php
session_start();
require 'database.php';

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

// Handle Archive Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive'])) {
    $user_id = $_POST['User_ID'];
    $sql = "UPDATE tbl_users SET role = 'archived' WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header('Location: admin_user.php'); // Redirect to refresh page
    exit;
}

// Handle Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $user_id = $_POST['User_ID'];

    // Delete from admin_info
    $sql = "DELETE FROM admin_info WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Delete from tbl_users
    $sql = "DELETE FROM tbl_users WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header('Location: admin_user.php'); // Redirect to refresh page
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    // Collect form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'admin'; // Role is 'admin' for this case
    $name = $_POST['name']; // Admin's name from the form
    $position = $_POST['position']; // Position from the form

    // Step 1: Insert the user into the tbl_users table
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password before inserting

    // Insert data into tbl_users
    $sql_user = "INSERT INTO tbl_users (email, password, role) 
                 VALUES ('$email', '$password_hash', '$role')";

    if ($conn->query($sql_user) === TRUE) {
        // Step 2: Get the User_ID of the newly inserted user
        $user_id = $conn->insert_id; // Get the last inserted User_ID
        
        // Step 3: Now insert into admin_info
        $sql_admin_info = "INSERT INTO admin_info (User_ID, name, position) 
                           VALUES ('$user_id', '$name', '$position')";
        
        if ($conn->query($sql_admin_info) === TRUE) {
            echo "New admin account created successfully!";
        } else {
            echo "Error inserting into admin_info: " . $conn->error;
        }
    } else {
        echo "Error inserting into tbl_users: " . $conn->error;
    }
}

// Fetch admin data (with admin_info details)
$sql = "SELECT 
            u.User_ID, 
            u.email, 
            ai.name, 
            ai.position 
        FROM tbl_users u
        JOIN admin_info ai ON u.User_ID = ai.User_ID
        WHERE u.role = 'admin'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .back-btn {
            font-size: 1rem;
            color: #004E98;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
        }

        .back-btn i {
            font-size: 1.2rem;
            margin-right: 5px;
        }

        .back-btn:hover {
            color: #003366;
            text-decoration: underline;
        }

        .btn-search {
            background-color: #004E98;
            color: white;
        }

        .btn-search:hover {
            background-color: #003366;
            color: white;
        }

        .btn-add-account {
            background-color: #004E98;
            color: white;
            font-weight: bold;
        }

        .btn-add-account:hover {
            background-color: #003366;
            color: white;
        }

        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <nav class="navbar navbar-expand-lg" style="background-color: #004E98; color: white;">
            <div class="container-fluid">
                <!-- Navbar Brand -->
                <a class="navbar-brand text-white" href="#">
                    <i class="fas fa-shield-alt"></i> Admin Dashboard
                </a>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </a>
                    <div class="dropdown-menu">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <a class="dropdown-item" href="profile_admin.php">Profile</a>
                            <a class="dropdown-item" href="?logout=true">Logout</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="login.php">Login</a>
                        <?php endif; ?>
                    </div>

                    </li>
            </div>
        </nav>
    </header>

    <!-- Back Button -->
    <div class="container mt-3">
        <a href="admin.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <div class="container mt-4">
    <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">ADMIN INFORMATION</h1>

    <!-- Add Account Button & Modal -->
    <button type="button" class="btn btn-add-account" data-bs-toggle="modal" data-bs-target="#addAccountModal">
        Add Account
    </button>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountModalLabel">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="admin_user.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Admin Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        <button type="submit" name="add_admin" class="btn btn-add-account">Add Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-12 text-end">
            <div class="input-group" style="max-width: 350px; display: inline-flex;">
                <input type="text" class="form-control" placeholder="Search" aria-label="Search">
                <button class="btn btn-search" type="button">Search</button>
            </div>
        </div>
    </div>

    <!-- Display Admin Data -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Email</th>
                <th>Admin Name</th>
                <th>Position</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($admin = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($admin['email']); ?></td>
                    <td><?= htmlspecialchars($admin['name']); ?></td>
                    <td><?= htmlspecialchars($admin['position']); ?></td>
                    <td>
                        <!-- View -->
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewProfileModal<?= $admin['User_ID']; ?>">View</button>

                        <!-- Archive -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="User_ID" value="<?= $admin['User_ID']; ?>">
                            <button type="submit" name="archive" class="btn btn-warning btn-sm">Archive</button>
                        </form>

                        <!-- Delete -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="User_ID" value="<?= $admin['User_ID']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewProfileModal<?= $admin['User_ID']; ?>" tabindex="-1" aria-labelledby="viewProfileModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">View Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Admin Name:</strong> <?= htmlspecialchars($admin['name']); ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']); ?></p>
                                <p><strong>Position:</strong> <?= htmlspecialchars($admin['position']); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
