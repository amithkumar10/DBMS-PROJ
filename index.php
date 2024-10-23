<?php
// Connect to the database (adjust the credentials accordingly)
$conn = new mysqli("localhost", "root", "", "movietickets");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movieName = $_POST['movieName'];
    $date = $_POST['date'];

    // Redirect to timeslots page with the selected movie and date
    header("Location: timeslots.php?movieName=" . urlencode($movieName) . "&date=" . urlencode($date));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.1/dist/tailwind.min.css" rel="stylesheet">
    <title>Movie Ticket Booking - Search</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-semibold text-center mb-6 text-gray-800">Search for Movies</h1>
        <form method="POST" action="index.php" class="space-y-4">
            <div>
                <label for="movieName" class="block text-gray-700 font-medium">Movie Name:</label>
                <input type="text" name="movieName" id="movieName" required class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="date" class="block text-gray-700 font-medium">Date:</label>
                <input type="date" name="date" id="date" required class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="text-center">
                <button type="submit" class="bg-blue-500 text-white font-medium py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">Search</button>
            </div>
        </form>
    </div>
</body>
</html>

