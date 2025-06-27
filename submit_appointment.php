<?php
// get_appointments.php
header('Content-Type: application/json');

// Assuming you are using a MySQL database
$servername = "localhost";  // Database server
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "qcu_guidance";   // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointments from the database
$sql = "SELECT appointment_date, time_slot FROM appointments";
$result = $conn->query($sql);

$appointments = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointments[] = [
            'title' => 'Booked',
            'start' => $row['appointment_date'] . 'T' . $row['time_slot'], // Format: 2024-11-15T10:00:00
            'end' => $row['appointment_date'] . 'T' . $row['time_slot'],   // Assuming one hour duration
            'color' => '#FF5733'  // Color for booked slots
        ];
    }
}

echo json_encode($appointments);

// Close the connection
$conn->close();
?>
