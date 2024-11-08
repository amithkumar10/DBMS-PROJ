<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Ticket Booking - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function showAdminLogin() {
            document.getElementById("adminEmailField").classList.remove("hidden");
            document.getElementById("userLoginForm").classList.add("hidden");
        }
        function showUserLogin() {
            document.getElementById("userLoginForm").classList.remove("hidden");
            document.getElementById("adminEmailField").classList.add("hidden");
        }
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full text-center">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Movie Ticket Booking</h1>
        <p class="mb-4 text-gray-600">Please select how you want to log in:</p>

        <div class="space-y-4">
            <button onclick="showUserLogin()" class="w-full py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 transition duration-200">
                Login as User
            </button>
            
            <button onclick="showAdminLogin()" class="w-full py-2 bg-green-500 text-white font-semibold rounded-md hover:bg-green-600 transition duration-200">
                Login as Admin
            </button>
        </div>

        <!-- User Login Form -->
        <form method="POST" id="userLoginForm" class="hidden mt-4">
            <input type="text" name="custname" placeholder="Enter Your Name" required 
                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
            <input type="text" name="phno" placeholder="Enter Your Phone Number" required 
                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
            <button type="submit" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300 w-full">
                Submit
            </button>
        </form>

        <!-- Admin Login Form -->
        <form action="admin.php" method="GET" id="adminEmailField" class="hidden mt-4">
            <input type="email" name="adminEmail" placeholder="Enter Admin Email" required 
                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 mb-4">
            <button type="submit" class="bg-green-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-green-600 transition duration-300 w-full">
                Submit
            </button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "movietickets");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Retrieve input values
        $custname = $_POST['custname'];
        $phno = $_POST['phno'];

        // Insert data into Customer table
        $stmt = $conn->prepare("INSERT INTO Customer (custname, phno) VALUES (?, ?)");
        $stmt->bind_param("ss", $custname, $phno);

        if ($stmt->execute()) {
            // Redirect to home.php if insertion is successful
            header("Location: home.php");
            exit();
        } else {
            echo "<p class='text-red-500 text-center mt-4'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>

</body>
</html>
