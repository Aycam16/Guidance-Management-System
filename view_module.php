<?php
// Get the module ID from the query parameter
$moduleID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Connect to the database
require_once 'database.php';

// Fetch the module details from the database
$sql = "SELECT * FROM modules WHERE ModuleID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $moduleID);
$stmt->execute();
$result = $stmt->get_result();
$module = $result->fetch_assoc();

if ($module) {
    // Fetch the file path
    $filePath = "uploads/" . $module['FilePath'];

    // Check if the file exists
    if (file_exists($filePath)) {
        // Directly display the file (for example, for PDFs)
        header('Content-Type: application/pdf');
        readfile($filePath);
    } else {
        echo "The requested file does not exist.";
    }
} else {
    echo "Module not found.";
}

$conn->close();
?>
