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
    <title>Movie Ticket Booking - Search</title>
</head>
<body>
    <h1>Search for Movies</h1>
    <form method="POST" action="index.php">
        <label for="movieName">Movie Name:</label>
        <input type="text" name="movieName" id="movieName" required><br><br>
        <label for="date">Date:</label>
        <input type="date" name="date" id="date" required><br><br>
        <button type="submit">Search</button>
    </form>
</body>
</html>
