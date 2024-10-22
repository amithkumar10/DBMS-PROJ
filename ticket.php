<?php 
include 'db_config.php'; // Include your database connection file

// Check if ticket_number is provided in the URL
if (isset($_GET['ticket_number'])) {
    $ticket_number = $_GET['ticket_number'];

    // Query to fetch booking details based on the ticket number
    $sql = "SELECT b.id, b.movie_name, b.seats, b.customer_name, b.phone_number, s.date, s.time_slot 
            FROM bookings b 
            JOIN showtimes s ON b.showtime_id = s.id 
            WHERE b.ticket_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ticket_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    // Check if booking was found
    if (!$booking) {
        echo "No booking found with the provided ticket number.";
        exit();
    }
} else {
    echo "Ticket number not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation</title>
    <script>
        function deleteTicket() {
            if (confirm("Are you sure you want to delete this ticket?")) {
                window.location.href = "delete_ticket.php?ticket_number=<?php echo htmlspecialchars($ticket_number); ?>";
            }
        }

        function redirectToIndex() {
            window.location.href = "index.php";
        }
    </script>
</head>
<body>
    <h1>Ticket Confirmation</h1>
    <p><strong>Ticket Number:</strong> <?php echo htmlspecialchars($ticket_number); ?></p>
    <p><strong>Movie Name:</strong> <?php echo htmlspecialchars($booking['movie_name']); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?></p>
    <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['time_slot']); ?></p>
    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($booking['phone_number']); ?></p>
    <p><strong>Seats Booked:</strong> <?php echo htmlspecialchars($booking['seats']); ?></p>

    <button onclick="redirectToIndex()">Go Back</button>
    <button onclick="deleteTicket()">Delete Ticket</button>
</body>
</html>
