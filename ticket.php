<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "movietickets");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve booking ID from URL
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : null;

if ($booking_id) {
    // Fetch booking details using the booking ID
    $sql = "
        SELECT 
            b.no_of_seats,
            c.custname AS customer_name,
            s.timing,
            s.date,
            md.mname AS movie_name,
            md.mgenre AS movie_genre
        FROM 
            bookings b
        JOIN 
            customer c ON b.customer_id = c.customer_id
        JOIN 
            Showtimes s ON b.showtime_id = s.showtime_id
        JOIN 
            MovieDetails md ON s.mid = md.mid
        WHERE 
            b.booking_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket_details = $result->fetch_assoc();

    if (!$ticket_details) {
        die("Ticket not found.");
    }
} else {
    die("Booking ID missing.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto my-12 p-8 bg-white shadow-lg rounded-lg max-w-md">
    <h2 class="text-2xl font-semibold text-center mb-6">Your Ticket</h2>

    <div class="p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($ticket_details['movie_name']); ?></h3>
        <p class="text-sm text-gray-600 mb-4"><strong>Genre:</strong> <?php echo htmlspecialchars($ticket_details['movie_genre']); ?></p>

        <div class="mb-4">
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($ticket_details['customer_name']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($ticket_details['date']); ?></p>
            <p><strong>Timing:</strong> <?php echo htmlspecialchars($ticket_details['timing']); ?></p>
            <p><strong>Seats:</strong> <?php echo htmlspecialchars($ticket_details['no_of_seats']); ?></p>
        </div>

        <p class="text-center text-lg font-bold text-blue-600 mt-6">Enjoy your movie!</p>
      
    </div>
    <div class="flex justify-center mt-6">
        <a href="http://localhost/movie_booking/index.php" class="inline-block">
            <button class="px-6 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition duration-200">
                Logout
            </button>
        </a>
    </div>
</div>

</body>
</html>
