<?php 
include 'db_config.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the data from the form
    $showtime_id = $_POST['showtime_id'];
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $num_of_seats = $_POST['num_of_seats'];

    // Fetch the movie name and available seats using the showtime_id
    $sql = "SELECT movie_name, available_seats FROM showtimes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie_row = $result->fetch_assoc();

    // Check if the movie and available seats were found
    if ($movie_row) {
        $movie_name = $movie_row['movie_name'];
        $available_seats = $movie_row['available_seats'];

        // Check if enough seats are available
        if ($num_of_seats > $available_seats) {
            echo "<script>alert('Not enough seats available. Only $available_seats seats left.'); window.history.back();</script>";
            exit();
        }

        // Generate a ticket number (could be a random number, timestamp, etc.)
        $ticket_number = uniqid("TKT_");

        // Start a transaction to ensure the booking and seat update happen together
        $conn->begin_transaction();

        try {
            // Insert the booking into the database
            $insert_sql = "INSERT INTO bookings (movie_name, showtime_id, customer_name, phone_number, seats, ticket_number) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sissis", $movie_name, $showtime_id, $customer_name, $phone_number, $num_of_seats, $ticket_number);

            if ($insert_stmt->execute()) {
                // Update the available seats
                $new_available_seats = $available_seats - $num_of_seats;
                $update_sql = "UPDATE showtimes SET available_seats = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ii", $new_available_seats, $showtime_id);
                $update_stmt->execute();

                // Commit the transaction
                $conn->commit();

                // Booking successful, redirect to the ticket page
                header("Location: ticket.php?ticket_number=$ticket_number");
                exit();
            } else {
                // Rollback the transaction on failure
                $conn->rollback();
                echo "Error: " . $insert_stmt->error;
            }
        } catch (Exception $e) {
            $conn->rollback(); // Rollback in case of any error
            echo "Error processing booking: " . $e->getMessage();
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
