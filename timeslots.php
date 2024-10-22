<?php 
include 'db_config.php'; 

// Check if GET data is set
if (!isset($_GET['movieName']) || !isset($_GET['date'])) {
    echo "Movie name or date not provided.";
    exit;
}

$movie_name = $_GET['movieName'];
$date = $_GET['date'];

$sql = "SELECT * FROM showtimes WHERE movie_name = ? AND date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $movie_name, $date);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css"> <!-- Optional CSS -->
    <title>Available Showtimes</title>
    <style>
        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin: 10px;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .card a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>
    <h1>Available Showtimes for <?php echo htmlspecialchars($movie_name); ?> on <?php echo htmlspecialchars($date); ?></h1>
    <div class="cards-container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h2><?php echo htmlspecialchars($row['time_slot']); ?></h2>
                <a href="book.php?showtime_id=<?php echo $row['id']; ?>">Book Now</a>
            </div>
        <?php endwhile; ?>
    </div>
    <a href="index.php">Back to Search</a>
</body>
</html>
