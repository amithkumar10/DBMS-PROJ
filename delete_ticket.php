<?php 
include 'db_config.php'; // Include your database connection file

if (isset($_GET['ticket_number'])) {
    $ticket_number = $_GET['ticket_number'];

    // Prepare the SQL statement to delete the booking
    $sql = "DELETE FROM bookings WHERE ticket_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ticket_number);
    
    if ($stmt->execute()) {
        // If the deletion is successful, redirect back to index.php with an alert message
        echo "<script>
            alert('Ticket has been deleted successfully.');
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000); // Redirects after 3 seconds
        </script>";
    } else {
        echo "Error deleting ticket: " . $stmt->error;
    }
} else {
    echo "Ticket number not provided.";
}
?>
