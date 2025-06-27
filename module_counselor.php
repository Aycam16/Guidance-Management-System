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

// Pagination variables
$limit = 5; // Number of modules per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Calculate offset

// Handle form submission to add a new module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_module'])) {
    $title = $_POST['moduleTitle'];
    $description = $_POST['moduleDescription'];
    $file = $_FILES['moduleFile'];
    $status = $_POST['moduleStatus'];  // Get status from form

    // Validate file upload
    $allowed_types = ['pdf', 'docx', 'pptx'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array($file_extension, $allowed_types)) {
        die('Invalid file type. Only PDF, DOCX, and PPTX files are allowed.');
    }

    if ($file['size'] > 5000000) { // Max file size: 5MB
        die('File is too large. Maximum size is 5MB.');
    }

    // Sanitize filename
    $file_name = basename($file["name"]);
    $file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);

    // Upload the file
    $target_dir = "uploads/";
    $target_file = $target_dir . $file_name;
    if (!move_uploaded_file($file["tmp_name"], $target_file)) {
        die('File upload failed.');
    }

    // Insert the module into the database
    $sql = "INSERT INTO modules (Title, Description, FilePath, Status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $description, $file_name, $status);
    if ($stmt->execute()) {
        header("Location: module_counselor.php");
        exit();
    } else {
        die("Error: " . $conn->error);
    }
}

// Handle form submission to edit a module
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit_module'])) {
    $moduleID = $_POST['moduleID'];
    $title = $_POST['moduleTitle'];
    $description = $_POST['moduleDescription'];
    $file = $_FILES['moduleFile'];
    $status = $_POST['moduleStatus'];  // Get status from form

    // Validate file upload (optional)
    $file_name = '';
    if ($file['name'] != '') {
        $allowed_types = ['pdf', 'docx', 'pptx'];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array($file_extension, $allowed_types)) {
            die('Invalid file type. Only PDF, DOCX, and PPTX files are allowed.');
        }

        if ($file['size'] > 5000000) { // Max file size: 5MB
            die('File is too large. Maximum size is 5MB.');
        }

        // Sanitize filename
        $file_name = basename($file["name"]);
        $file_name = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $file_name);

        // Upload the file
        $target_dir = "uploads/";
        $target_file = $target_dir . $file_name;
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            die('File upload failed.');
        }
    }

    // Update the module in the database
    if ($file_name == '') {
        // No new file uploaded, so we keep the old file
        $sql = "UPDATE modules SET Title = ?, Description = ?, Status = ? WHERE ModuleID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $description, $status, $moduleID);
    } else {
        // New file uploaded, update the file path
        $sql = "UPDATE modules SET Title = ?, Description = ?, FilePath = ?, Status = ? WHERE ModuleID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $description, $file_name, $status, $moduleID);
    }

    if ($stmt->execute()) {
        header("Location: module_counselor.php");
        exit();
    } else {
        die("Error: " . $conn->error);
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_delete = "DELETE FROM modules WHERE ModuleID = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        header("Location: module_counselor.php");
        exit();
    } else {
        die("Error: " . $conn->error);
    }
}

// Fetch modules with pagination
$sql = "SELECT * FROM modules ORDER BY ModuleID DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Pagination: Get the total number of modules
$sql_total = "SELECT COUNT(*) AS total FROM modules";
$total_result = $conn->query($sql_total);
$total_row = $total_result->fetch_assoc();
$total_modules = $total_row['total'];
$total_pages = ceil($total_modules / $limit);

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
        margin-bottom: 350px; /* Add space to prevent overlapping of content with footer */
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

        .btn-primary,
        .btn-outline-secondary,
        .btn-secondary {
            background-color: #004E98;
            border: none;
            color: white;
        }

        .btn-primary:hover,
        .btn-outline-secondary:hover,
        .btn-secondary:hover {
            background-color: #003366;
            color: white;
        }

        table thead th {
            background-color: #D9E7FF;
            color: #004E98;
            text-align: center;
        }

        table tbody td {
            text-align: center;
        }

        .status-posted {
            background-color: #D4F4DD;
            color: #2D8F47;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 0.9em;
        }

         .filters-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filters-container .left-controls {
            display: flex;
            gap: 10px;
        }

        .filters-container .right-control {
            text-align: right;
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

        .status-posted {
            background-color: #D4F4DD;
            color: #2D8F47;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 0.9em;
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

    <div class="container mt-5">
    <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">MODULES</h1>

    <!-- Filters and Add Module Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">+ Add Module</button>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-secondary" type="button">Search</button>
        </div>
    </div>

    <!-- Table -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>File</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Title']) ?></td>
                    <td><?= htmlspecialchars($row['Description']) ?></td>
                    <td>
                        <a href="uploads/<?= htmlspecialchars($row['FilePath']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">View</a>
                    </td>
                    <td><?= htmlspecialchars($row['Status']) ?></td>
                    <td>
                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editModuleModal"
                            data-moduleid="<?= $row['ModuleID'] ?>"
                            data-moduletitle="<?= htmlspecialchars($row['Title']) ?>"
                            data-moduledescription="<?= htmlspecialchars($row['Description']) ?>"
                            data-modulestatus="<?= htmlspecialchars($row['Status']) ?>"
                        >
                            Edit
                        </button>
                        <a href="module_counselor.php?delete_id=<?= $row['ModuleID'] ?>" class="btn btn-outline-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No modules found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

    <!-- Pagination -->
    <div class="d-flex justify-content-between">
        <span>Showing <?= ($offset + 1) ?>-<?= min($offset + $limit, $total_modules) ?> of <?= $total_modules ?></span>
        <nav>
            <ul class="pagination">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- Modal to Edit Module -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="moduleID" id="moduleID">
                    <div class="mb-3">
                        <label for="moduleTitle" class="form-label">Module Title</label>
                        <input type="text" class="form-control" id="moduleTitle" name="moduleTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="moduleDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="moduleDescription" name="moduleDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="moduleFile" class="form-label">Upload New File (Optional)</label>
                        <input class="form-control" type="file" id="moduleFile" name="moduleFile">
                    </div>
                    <div class="mb-3">
                        <label for="moduleStatus" class="form-label">Status</label>
                        <select class="form-control" id="moduleStatus" name="moduleStatus" required>
                            <option value="draft">Draft</option>
                            <option value="post">Post</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit_edit_module">Update Module</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal to Add Module -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModuleModalLabel">Add Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="moduleTitle" class="form-label">Module Title</label>
                        <input type="text" class="form-control" id="moduleTitle" name="moduleTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="moduleDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="moduleDescription" name="moduleDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="moduleFile" class="form-label">Upload File</label>
                        <input class="form-control" type="file" id="moduleFile" name="moduleFile" required>
                    </div>
                    <div class="mb-3">
                        <label for="moduleStatus" class="form-label">Status</label>
                        <select class="form-control" id="moduleStatus" name="moduleStatus" required>
                            <option value="draft">Draft</option>
                            <option value="post">Post</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit_module">Save Module</button>
                </form>
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
    <script >
        // Populate View Module Modal
function populateViewModal(id) {
    document.getElementById('viewTitle').innerText = document.getElementById('moduleTitle_' + id).innerText;
    document.getElementById('viewDescription').innerText = document.getElementById('moduleDescription_' + id).innerText;
    document.getElementById('viewPostedBy').innerText = document.getElementById('modulePostedBy_' + id).innerText;
    document.getElementById('viewDatePosted').innerText = document.getElementById('moduleDatePosted_' + id).innerText;
    document.getElementById('viewStatus').innerText = document.getElementById('moduleStatus_' + id).innerText;
}

// Populate Edit Module Modal
function populateEditModal(id) {
    document.getElementById('editModuleId').value = id;
    document.getElementById('editModuleTitle').value = document.getElementById('moduleTitle_' + id).innerText;
    document.getElementById('editModuleDescription').value = document.getElementById('moduleDescription_' + id).innerText;
}
function setDeleteId(id) {
    document.querySelector('#deleteConfirmationModal input[name="delete_id"]').value = id;
}
    </script>

<script>
function changeStatus(moduleId, currentStatus) {
    const newStatus = currentStatus === 'Posted' ? 'Draft' : 'Posted';

    fetch('yourpage.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            change_status: true,
            module_id: moduleId,
            new_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to update the table
        } else {
            alert('Failed to update status.');
        }
    });
}
</script>
<script>
    // JavaScript to pre-fill the edit module form with existing data
    $('#editModuleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var moduleID = button.data('moduleid');
        var moduleTitle = button.data('moduletitle');
        var moduleDescription = button.data('moduledescription');
        var moduleStatus = button.data('modulestatus');

        // Fill the modal fields with the existing data
        var modal = $(this);
        modal.find('#moduleID').val(moduleID);
        modal.find('#moduleTitle').val(moduleTitle);
        modal.find('#moduleDescription').val(moduleDescription);
        modal.find('#moduleStatus').val(moduleStatus);  // Set the status dropdown value
    });
</script>

</body>
</html>
