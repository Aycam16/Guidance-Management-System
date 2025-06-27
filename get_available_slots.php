<?php
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    // Logic to fetch available slots from the database based on the selected date.
    // Example response:
    echo json_encode([
        'available_slots' => [
            '9:00 AM - 10:00 AM',
            '10:00 AM - 11:00 AM',
            '1:00 PM - 2:00 PM'
        ]
    ]);
} else {
    echo json_encode(['available_slots' => []]);
}
?>
