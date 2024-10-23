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
    <style>
        .ticket {
            border: 3px solid black;
            padding: 5%;
        }
    </style>
    <script>
        function deleteTicket() {
            if (confirm("Are you sure you want to delete this ticket?")) {
                window.location.href = "delete_ticket.php?ticket_number=<?php echo htmlspecialchars($ticket_number); ?>";
            }
        }

        function redirectToIndex() {
            window.location.href = "index.php";
        }

        // Function to download ticket as a .txt file
        function downloadTicket() {
            // Extract text from the ticket div
            const ticketContent = document.querySelector('.ticket').innerText;

            // Format the ticket content in a box-like format
            const formattedTicket = `
*****************************
           TICKET
*****************************
${ticketContent}
*****************************
`;

            // Create a blob object with the ticket content
            const blob = new Blob([formattedTicket], { type: 'text/plain' });
            const link = document.createElement('a');

            // Create a download link
            link.href = URL.createObjectURL(blob);
            link.download = 'ticket_<?php echo htmlspecialchars($ticket_number); ?>.txt'; // Filename with ticket number
            link.click(); // Trigger the download
        }
    </script>
</head>
<body>
    <h1>Ticket Confirmation</h1>
    <div class="ticket">
        <p><strong>Ticket Number:</strong> <?php echo htmlspecialchars($ticket_number); ?></p>
        <p><strong>Movie Name:</strong> <?php echo htmlspecialchars($booking['movie_name']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($booking['date']); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($booking['time_slot']); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
        <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($booking['phone_number']); ?></p>
        <p><strong>Seats Booked:</strong> <?php echo htmlspecialchars($booking['seats']); ?></p>
    </div>

    <button onclick="redirectToIndex()">Go Back</button>
    <button onclick="deleteTicket()">Delete Ticket</button>
    <button onclick="downloadTicket()">Download Ticket</button> <!-- Download Ticket Button -->
</body>
</html>
