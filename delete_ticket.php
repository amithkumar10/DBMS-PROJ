<?php 
include 'db_config.php'; // Include your database connection file

if (isset($_GET['ticket_number'])) {
    $ticket_number = $_GET['ticket_number'];

    // Step 1: Fetch the number of seats and showtime_id associated with the ticket
    $sql = "SELECT seats, showtime_id FROM bookings WHERE ticket_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ticket_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket = $result->fetch_assoc();

    if ($ticket) {
        $seats = $ticket['seats'];
        $showtime_id = $ticket['showtime_id'];

        // Step 2: Prepare the SQL statement to delete the booking
        $delete_sql = "DELETE FROM bookings WHERE ticket_number = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("s", $ticket_number);
        
        if ($delete_stmt->execute()) {
            // Step 3: Update the available seats in the showtimes table
            $update_sql = "UPDATE showtimes SET available_seats = available_seats + ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $seats, $showtime_id);
            $update_stmt->execute();

            // If the deletion and update are successful, redirect back to index.php with an alert message
            echo "<script>
                alert('Ticket has been deleted successfully.');
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000); // Redirects after 3 seconds
            </script>";
        } else {
            echo "Error deleting ticket: " . $delete_stmt->error;
        }
    } else {
        echo "Ticket not found.";
    }
} else {
    echo "Ticket number not provided.";
}
?>
