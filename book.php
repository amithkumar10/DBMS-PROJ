<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "movietickets");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve showtime_id from URL
$showtime_id = $_GET['showtime_id'];

// Fetch the movie name based on the showtime_id
$sql = "SELECT m.mname FROM Showtimes s JOIN MovieDetails m ON s.mid = m.mid WHERE s.showtime_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$stmt->bind_result($movieName);
$stmt->fetch();
$stmt->close();

// Retrieve available seats for the specific showtime
$sql = "SELECT available_seats FROM Showtimes WHERE showtime_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$stmt->bind_result($available_seats);
$stmt->fetch();
$stmt->close();

// Define posters based on movie name
$posterImage = '';
if (in_array($showtime_id, [1, 2, 3])) {  // Showtime IDs 1 to 3 for Inception
    $posterImage = 'https://getwallpapers.com/wallpaper/full/6/9/4/1109843-download-inception-wallpaper-1920x1200-for-mac.jpg';  // Inception Poster
} elseif (in_array($showtime_id, [4, 5, 6])) {  // Showtime IDs 4 to 6 for Dark Knight
    $posterImage = 'https://i.pinimg.com/564x/5e/60/a2/5e60a29eb075da8d2032ea292549b35f.jpg';  // Dark Knight Poster
}

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
<body class="bg-gray-300">

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-black via-gray-900 to-gray-800 p-8 border-b border-gray-800">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo Image -->
            <a href="#" class="flex items-center">
                <img src="CiNEBOOK.jpeg" alt="CineBook Logo" class="h-8 mr-2">
                <!-- Logo Text -->
                <span class="text-white text-xl font-bold">CineBook</span>
            </a>
        </div>
    </nav>

    <!-- Movie Poster Section -->
    <?php if ($posterImage): ?>
        <div class="bg-black text-center py-4">
            <img src="<?php echo $posterImage; ?>" alt="Movie Poster" class="mx-auto w-full max-w-2xl rounded-lg shadow-lg">
        </div>
    <?php endif; ?>

    <div class="bg-gray-200 p-10 mb-20 relative">
    <h1 class="text-3xl font-bold">Book Movie Tickets: <?php echo $movieName; ?></h1>
    <!-- Button for ticket price -->
    <button class="absolute top-10 right-10 bg-blue-500 text-white font-semibold py-2 px-4 rounded-md" onclick="document.getElementById('booking-card').scrollIntoView();">
        Price: ₹ 270
    </button>
</div>

    <!-- Booking Form -->
    <div id="booking-card" class="flex justify-center min-h-md mb-10">
        <div class="w-full max-w-md bg-gray-200 rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-700 mb-6 text-center">Cinebook</h2>
            <form method="post" action="book.php?showtime_id=<?php echo $showtime_id; ?>" oninput="updateTotalCost()">
                <div class="mb-4">
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" required 
                           class="w-full bg-gray-200 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" required
                           class="w-full bg-gray-200 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                </div>
                <div class="mb-6">
                    <label for="num_of_seats" class="block text-sm font-medium text-gray-700 mb-2">Number of Seats</label>
                    <input type="number" id="num_of_seats" name="num_of_seats" min="1" max="<?php echo $available_seats; ?>" required
                           class="w-full bg-gray-200 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Price: </label>
                    <span id="total-price" class="text-lg font-semibold">₹ 270</span>
                </div>
                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-md transition-colors duration-300 focus:outline-none">
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>

    <script>
        // Function to update total cost based on number of seats
        function updateTotalCost() {
            const ticketPrice = 270;
            const numOfSeats = document.getElementById('num_of_seats').value;
            const totalCost = ticketPrice * numOfSeats;
            document.getElementById('total-price').textContent = '₹ '+ totalCost ;
        }
    </script>

</body>
</html>
