<?php 
include 'db_config.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the data from the form
    $showtime_id = $_POST['showtime_id'];
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $num_of_seats = $_POST['num_of_seats'];

    // Fetch the movie name using the showtime_id
    $sql = "SELECT movie_name FROM showtimes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie_row = $result->fetch_assoc();

    // Check if the movie name was found
    if ($movie_row) {
        $movie_name = $movie_row['movie_name'];

        // Generate a ticket number (could be a random number, timestamp, etc.)
        $ticket_number = uniqid("TKT_");

        // Insert the booking into the database
        $insert_sql = "INSERT INTO bookings (movie_name, showtime_id, customer_name, phone_number, seats, ticket_number) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sissis", $movie_name, $showtime_id, $customer_name, $phone_number, $num_of_seats, $ticket_number);

        if ($insert_stmt->execute()) {
            // Booking successful, redirect to the ticket page
            header("Location: ticket.php?ticket_number=$ticket_number");
            exit();
        } else {
            echo "Error: " . $insert_stmt->error;
        }
    } else {
        echo "Movie not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets</title>
</head>
<body>
    <h1>Book Your Tickets</h1>
    <form method="POST" action="book.php">
        <input type="hidden" name="showtime_id" value="<?php echo htmlspecialchars($_GET['showtime_id']); ?>">
        
        <label for="customer_name">Name:</label>
        <input type="text" name="customer_name" required><br><br>
        
        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" required><br><br>
        
        <label for="num_of_seats">Number of Seats:</label>
        <input type="number" name="num_of_seats" required><br><br>
        
        <button type="submit">Submit</button>
    </form>
</body>
</html>
