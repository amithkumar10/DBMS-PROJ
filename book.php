<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "movietickets");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve showtime_id and available_seats
$showtime_id = $_GET['showtime_id'];
$available_seats = 50; // Fetch this value from the Showtimes table as per your application logic

// Handle form submission for booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $num_of_seats = $_POST['num_of_seats'];

    // Check if enough seats are available
    if ($num_of_seats > $available_seats) {
        echo "<script>alert('Not enough seats available.'); window.history.back();</script>";
        exit();
    }

    // Start a transaction to ensure booking and seat update happen together
    $conn->begin_transaction();

    try {
        // Insert customer details
        $insert_customer_sql = "INSERT INTO customer (custname, phno) VALUES (?, ?)";
        $customer_stmt = $conn->prepare($insert_customer_sql);
        $customer_stmt->bind_param("ss", $customer_name, $phone_number);
        $customer_stmt->execute();

        // Get the customer_id of the new customer
        $customer_id = $conn->insert_id;

        // Insert booking into the database
        $insert_booking_sql = "INSERT INTO bookings (showtime_id, customer_id, no_of_seats) VALUES (?, ?, ?)";
        $booking_stmt = $conn->prepare($insert_booking_sql);
        $booking_stmt->bind_param("iii", $showtime_id, $customer_id, $num_of_seats);

        if ($booking_stmt->execute()) {
            // Get the booking_id of the newly created booking
            $booking_id = $conn->insert_id;

            // Update available seats
            $new_available_seats = $available_seats - $num_of_seats;
            $update_seats_sql = "UPDATE Showtimes SET available_seats = ? WHERE showtime_id = ?";
            $update_stmt = $conn->prepare($update_seats_sql);
            $update_stmt->bind_param("ii", $new_available_seats, $showtime_id);
            $update_stmt->execute();

            // Commit transaction
            $conn->commit();

            // Redirect to the ticket page with the booking_id
            header("Location: ticket.php?booking_id=$booking_id");
            exit();
        } else {
            // Rollback transaction if booking insertion failed
            $conn->rollback();
            echo "Error: " . $booking_stmt->error;
        }
    } catch (Exception $e) {
        // Rollback transaction in case of an exception
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6 text-center">Book Your Tickets</h2>
        <form method="post" action="book.php?showtime_id=<?php echo $showtime_id; ?>">
            <div class="mb-4">
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="num_of_seats" class="block text-sm font-medium text-gray-700 mb-2">Number of Seats</label>
                <input type="number" id="num_of_seats" name="num_of_seats" min="1" max="<?php echo $available_seats; ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-md transition-colors duration-300 focus:outline-none">
                Confirm Booking
            </button>
        </form>
    </div>
</body>
</html>
