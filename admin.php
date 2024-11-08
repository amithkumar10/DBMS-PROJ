<?php
session_start();  // Start the session

// Check if the admin is logged in
// If the admin is logged in, proceed to show the dashboard
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-50">

<div class="container mx-auto my-12 p-8 bg-white shadow-lg rounded-lg">
    <h2 class="text-3xl font-bold text-center text-blue-600 mb-8">Welcome, Admin</h2>

    <?php
    // Database connection
    $conn = new mysqli("localhost", "root", "", "movietickets");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>

    <!-- Table Component -->
    <?php
    function renderTable($title, $headers, $rows) {
        echo "
        <div class='mb-10'>
            <h3 class='text-2xl font-semibold text-blue-600 mb-4'>$title</h3>
            <div class='overflow-x-auto'>
                <table class='min-w-full border border-gray-300 rounded-lg shadow-sm'>
                    <thead class='bg-blue-600 text-white'>
                        <tr>";
        foreach ($headers as $header) {
            echo "<th class='px-6 py-3 border-b text-left font-semibold'>$header</th>";
        }
        echo "
                        </tr>
                    </thead>
                    <tbody class='bg-blue-50'>";
        foreach ($rows as $row) {
            echo "<tr class='hover:bg-blue-100'>";
            foreach ($row as $cell) {
                echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
        echo "
                    </tbody>
                </table>
            </div>
        </div>";
    }
    ?>

    <!-- Admin Table -->
    <?php
    $result = $conn->query("SELECT * FROM Admin");
    $adminRows = [];
    while ($row = $result->fetch_assoc()) {
        $adminRows[] = [$row['aid'], $row['aname'], $row['aemail']];
    }
    renderTable("Admin Table", ["Admin ID", "Name", "Email"], $adminRows);
    ?>

    <!-- MovieDetails Table -->
    <?php
    $result = $conn->query("SELECT * FROM MovieDetails");
    $movieRows = [];
    while ($row = $result->fetch_assoc()) {
        $movieRows[] = [$row['mid'], $row['mname'], $row['mgenre']];
    }
    renderTable("Movie Details Table", ["Movie ID", "Name", "Genre"], $movieRows);
    ?>

    <!-- Showtimes Table -->
    <?php
    $result = $conn->query("SELECT * FROM Showtimes");
    $showtimeRows = [];
    while ($row = $result->fetch_assoc()) {
        $showtimeRows[] = [$row['showtime_id'], $row['mid'], $row['timing'], $row['available_seats'], $row['date']];
    }
    renderTable("Showtimes Table", ["Showtime ID", "Movie ID", "Timing", "Available Seats", "Date"], $showtimeRows);
    ?>

    <!-- Customer Table -->
    <?php
    $result = $conn->query("SELECT * FROM Customer");
    $customerRows = [];
    while ($row = $result->fetch_assoc()) {
        $customerRows[] = [$row['customer_id'], $row['custname'], $row['phno']];
    }
    renderTable("Customer Table", ["Customer ID", "Name", "Phone Number"], $customerRows);
    ?>

    <!-- Bookings Table -->
    <?php
    $result = $conn->query("SELECT * FROM bookings");
    $bookingRows = [];
    while ($row = $result->fetch_assoc()) {
        $bookingRows[] = [$row['booking_id'], $row['customer_id'], $row['no_of_seats'], $row['showtime_id']];
    }
    renderTable("Bookings Table", ["Booking ID", "Customer ID", "Number of Seats", "Showtime ID"], $bookingRows);
    ?>

    <!-- JOIN Table -->
    <?php
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
    ";
    $result = $conn->query($sql);
    $joinRows = [];
    while ($row = $result->fetch_assoc()) {
        $joinRows[] = [$row['no_of_seats'], $row['customer_name'], $row['movie_name'], $row['timing'], $row['date'], $row['movie_genre']];
    }
    renderTable("Joined Table", ["Number of Seats", "Customer Name", "Movie Name", "Timing", "Date", "Genre"], $joinRows);
    ?>

    <div class="text-center mt-8">
        <a href="index.php" class="text-blue-500 hover:underline font-semibold">Logout</a>
    </div>
</div>

</body>
</html>
