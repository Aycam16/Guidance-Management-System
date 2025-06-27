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
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            error_log("Error archiving user: " . $stmt->error);
        }
    }
    header('Location: ' . filter_var('counselors.php', FILTER_SANITIZE_URL));
    exit;
}

// Handle Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $user_id = $_POST['User_ID'];
    
    // Delete from counselor_info
    $sql = "DELETE FROM counselor_info WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            error_log("Error deleting from counselor_info: " . $stmt->error);
        }
    }

    // Delete from tbl_users
    $sql = "DELETE FROM tbl_users WHERE User_ID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            error_log("Error deleting from tbl_users: " . $stmt->error);
        }
    }

    header('Location: ' . filter_var('counselors.php', FILTER_SANITIZE_URL));
    exit;
}

// Handle Add Counselor Action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_counselor'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'counselor';
    $name = $_POST['name'];
    $department = $_POST['department'];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into tbl_users
    $sql_user = "INSERT INTO tbl_users (email, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_user);
    if ($stmt) {
        $stmt->bind_param("sss", $email, $password_hash, $role);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into counselor_info
            $sql_counselor_info = "INSERT INTO counselor_info (User_ID, name, department) VALUES (?, ?, ?)";
            $stmt_info = $conn->prepare($sql_counselor_info);
            if ($stmt_info) {
                $stmt_info->bind_param("iss", $user_id, $name, $department);
                if ($stmt_info->execute()) {
                    $_SESSION['success_message'] = "New counselor account created successfully!";
                } else {
                    error_log("Error inserting into counselor_info: " . $stmt_info->error);
                }
            }
        } else {
            error_log("Error inserting into tbl_users: " . $stmt->error);
        }
    }
}

// Fetch Counselor Data with Search Functionality
$search = $_GET['search'] ?? '';
$sql = "SELECT u.User_ID, u.email, ci.name, ci.department 
        FROM tbl_users u
        JOIN counselor_info ci ON u.User_ID = ci.User_ID
        WHERE u.role = 'counselor' AND (ci.name LIKE ? OR ci.department LIKE ? OR u.email LIKE ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = [];
}
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

    <!-- HTML for Counselor Management -->
<div class="container mt-4">
    <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">COUNSELOR INFORMATION</h1>

    <!-- Success Message -->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>

    <!-- Add Counselor Modal -->
    <button type="button" class="btn btn-add-account" data-bs-toggle="modal" data-bs-target="#addAccountModal">Add Account</button>
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="counselors.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Counselor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label>Department</label>
                            <input type="text" class="form-control" name="department" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_counselor" class="btn btn-add-account">Add Counselor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Form -->
<div class="row mb-4">
    <div class="col-12 text-end">
        <form method="GET" class="d-inline-flex" style="max-width: 350px;">
            <input type="text" class="form-control" name="search" placeholder="Search by name, email, or department" value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-search ms-2">Search</button>
        </form>
    </div>
</div>

    <!-- Display Counselor Data -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($counselor = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($counselor['email']); ?></td>
                    <td><?= htmlspecialchars($counselor['name']); ?></td>
                    <td><?= htmlspecialchars($counselor['department']); ?></td>
                    <td>
                        <!-- View Modal -->
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewProfileModal<?= $counselor['User_ID']; ?>">View</button>

                        <!-- Archive -->
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to archive this counselor?');">
                            <input type="hidden" name="User_ID" value="<?= $counselor['User_ID']; ?>">
                            <button type="submit" name="archive" class="btn btn-warning btn-sm">Archive</button>
                        </form>

                        <!-- Delete -->
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this counselor?');">
                            <input type="hidden" name="User_ID" value="<?= $counselor['User_ID']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <!-- View Profile Modal -->
                <div class="modal fade" id="viewProfileModal<?= $counselor['User_ID']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>View Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Name:</strong> <?= htmlspecialchars($counselor['name']); ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($counselor['email']); ?></p>
                                <p><strong>Department:</strong> <?= htmlspecialchars($counselor['department']); ?></p>
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
