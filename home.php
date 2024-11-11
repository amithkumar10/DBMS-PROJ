<?php
// Connect to the database
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
    <title>Search Movies - CineBook</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body  class="bg-gradient-to-r from-gray-400  to-gray-600 bg-cover bg-center bg-no-repeat relative" style="min-height: 100vh;"

    <!-- Navbar -->
    <nav class="bg-gradient-to-br from-gray-800 via-gray-900 to-black p-8">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo Image -->
        <a href="#" class="flex items-center">
            <img src="CiNEBOOK.jpeg" alt="CineBook Logo" class="h-8 mr-2">
            <!-- Logo Text -->
            <span class="text-white text-xl font-bold">CineBook</span>
        </a>
    </div>
</nav>


    <!-- Search Card -->
    <div class="absolute inset-0 flex items-center justify-center py-12">
        <div class="bg-gradient-to-br from-gray-800 via-gray-900 to-black shadow-lg rounded-lg p-8 max-w-md w-full text-center relative z-10">
            <h1 class="text-3xl font-semibold text-white mb-6">Search for Movies</h1>
            <form method="POST" action="home.php" class="space-y-4">
                <div>
                    <label for="movieName" class="block text-gray-300 font-medium">Movie Name:</label>
                    <input type="text" name="movieName" id="movieName" required class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date" class="block text-gray-300 font-medium">Date:</label>
                    <input type="date" name="date" id="date" required class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-blue-500 text-white font-medium py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">Search</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
