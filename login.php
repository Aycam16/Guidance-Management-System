<?php
session_start();
require 'database.php';

// Check if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $role = $_SESSION['role'];  
    if ($role === 'student') {
        header('Location: index.php');
    } elseif ($role === 'counselor') {
        header('Location: index_counselor.php');
    } elseif ($role === 'staff') {
        header('Location: index_staff.php');
    } elseif ($role === 'faculty') {
        header('Location: index_faculty.php');
    } elseif ($role === 'admin') {
        header('Location: admin.php');
    }
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate role and query appropriate table
    switch ($role) {
        case 'student':
            $query = "SELECT * FROM students WHERE email = ?";
            break;
        case 'counselor':
            $query = "SELECT * FROM counselors WHERE email = ?";
            break;
        case 'staff':
            $query = "SELECT * FROM staff WHERE email = ?";
            break;
        case 'faculty':
            $query = "SELECT * FROM faculty WHERE email = ?";
            break;
        case 'admin':
            $query = "SELECT * FROM admin WHERE email = ?";
            break;
        default:
            $error_message = "Invalid role selected.";
            break;
    }

    if (isset($query)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Password validation
            if ($password === $user['password']) {
                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $role;

                // Redirect based on role
                if ($role === 'student') {
                    header('Location: index.php');
                } elseif ($role === 'counselor') {
                    header('Location: index_counselor.php');
                } elseif ($role === 'staff') {
                    header('Location: index_staff.php');
                } elseif ($role === 'faculty') {
                    header('Location: index_faculty.php');
                } elseif ($role === 'admin') {
                    header('Location: admin.php');
                }
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "Invalid email.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        
        <label for="role">Role:</label>
        <select name="role">
            <option value="student">Student</option>
            <option value="counselor">Counselor</option>
            <option value="staff">Staff</option>
            <option value="faculty">Faculty</option>
            <option value="admin">Admin</option>
        </select><br>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
