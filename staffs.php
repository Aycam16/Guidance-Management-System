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
                    <a class="nav-link dropdown-toggle text-white" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="profile.php">Profile</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
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
        <h1 class="text-center mb-4" style="color: #004E98; font-weight: bold;">STAFFS INFORMATION</h1>

        <!-- Add Account Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-add-account" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="fas fa-user-plus"></i> Add Account
            </button>

            <!-- Search Box -->
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" placeholder="Search" aria-label="Search">
                <button class="btn btn-search" type="button">Search</button>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Example data for students
                $counselors = [
                    [
                        "employee_id" => "11-1111",
                        "last_name" => "Dela Cruz",
                        "first_name" => "Juan",
                        "email" => "juan_delacruz@gmail.com",
                        "position" => "Counselor",
                    ],
                    [
                        "employee_id" => "11-2222",
                        "last_name" => "Santos",
                        "first_name" => "Maria",
                        "email" => "maria_santos@gmail.com",
                        "position" => "Counselor",
                    ]
                ];

                foreach ($counselors as $counselors) {
                    echo "<tr>";
                    echo "<td>{$counselors['employee_id']}</td>";
                    echo "<td>{$counselors['last_name']}</td>";
                    echo "<td>{$counselors['first_name']}</td>";
                    echo "<td>{$counselors['email']}</td>";
                    echo "<td>{$counselors['position']}</td>";
                    echo "<td>
                            <button class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#viewProfileModal'>View</button>
                            <button class='btn btn-warning btn-sm'>Archive</button>
                            <button class='btn btn-danger btn-sm'>Delete</button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountModalLabel">Add New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Profile Modal -->
<div class="modal fade" id="viewProfileModal" tabindex="-1" aria-labelledby="viewProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="viewProfileModalLabel">View Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="container">
                    <div class="profile-container">
                        <!-- Profile Header -->
                        <div class="profile-header d-flex justify-content-between align-items-center">
                            <div>
                                <img id="profile-image" src="userpic.png" alt="Profile Image" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;" />
                                <h2 id="profile-name">John Doe</h2>
                                <p id="profile-email">john.doe@gmail.com</p>
                                <p id="profile-position">Staff</p>
                            </div>
                        </div>

                        <!-- Profile Details -->
                        <form>
                            <div class="profile-info row g-3 mt-3">
                                <div class="col-md-6">
                                    <label for="last-name" class="form-label">Last Name</label>
                                    <input type="text" id="last-name" class="form-control" value="Doe" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="first-name" class="form-label">First Name</label>
                                    <input type="text" id="first-name" class="form-control" value="John" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="middle-name" class="form-label">Middle Name</label>
                                    <input type="text" id="middle-name" class="form-control" value="Michael" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="suffix" class="form-label">Suffix</label>
                                    <input type="text" id="suffix" class="form-control" value="Jr." disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" class="form-control" value="john.doe@gmail.com" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="birthday" class="form-label">Birthday</label>
                                    <input type="date" id="birthday" class="form-control" value="1990-04-15" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" class="form-select" disabled>
                                        <option value="male" selected>Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="position" class="form-label">Position</label>
                                    <select id="position" class="form-select" disabled>
                                        <option value="cc" >Counselor</option>
                                        <option value="st"selected>Staff</option>
                                        <option value="fa">Faculty</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" class="form-select" disabled>
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
