<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "movietickets");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the movie name and date from URL parameters
$movieName = isset($_GET['movieName']) ? $_GET['movieName'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Fetch movie details based on movieName
$sql = "SELECT * FROM MovieDetails WHERE mname LIKE ?";
$stmt = $conn->prepare($sql);
$movieNameParam = "%$movieName%";
$stmt->bind_param("s", $movieNameParam);
$stmt->execute();
$movieResult = $stmt->get_result();

// Fetch showtimes based on movie and date
$sqlShowtimes = "SELECT s.showtime_id, s.timing, s.available_seats, s.date, m.mname 
                 FROM Showtimes s
                 JOIN MovieDetails m ON s.mid = m.mid
                 WHERE m.mname LIKE ? AND s.date = ?";
$stmtShowtimes = $conn->prepare($sqlShowtimes);
$stmtShowtimes->bind_param("ss", $movieNameParam, $date);
$stmtShowtimes->execute();
$showtimesResult = $stmtShowtimes->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Showtimes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white text-lg font-bold">Movie Ticket Booking</a>
            <ul class="flex space-x-4">
                <li><a href="#" class="text-white hover:underline">Home</a></li>
                <li><a href="#" class="text-white hover:underline">Movies</a></li>
                <li><a href="#" class="text-white hover:underline">Bookings</a></li>
                <li><a href="#" class="text-white hover:underline">Contact</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto my-12">
        <h2 class="text-3xl font-semibold text-center mb-6">Available Showtimes for "<?php echo htmlspecialchars($movieName); ?>" on <?php echo htmlspecialchars($date); ?></h2>

        <?php if ($showtimesResult->num_rows > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($showtime = $showtimesResult->fetch_assoc()): ?>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($showtime['timing']); ?></h3>
                        <p class="text-gray-600">Seats Available: <?php echo htmlspecialchars($showtime['available_seats']); ?></p>
                        <a href="book.php?showtime_id=<?php echo $showtime['showtime_id']; ?>" class="mt-4 block bg-blue-500 text-white py-2 px-4 rounded-md text-center hover:bg-blue-600">
                            Book Ticket
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-red-600">No showtimes available for this movie on the selected date.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-blue-600 text-white p-4 text-center">
        <p>&copy; 2024 Movie Ticket Booking. All rights reserved.</p>
    </footer>

</body>
</html>
