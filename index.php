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
            // No action needed for "Login as User" as it redirects directly
            window.location.href = "home.php"; // Redirect to home.php
        }
    </script>
</head>
<body class="bg-cover bg-center bg-no-repeat flex items-center justify-center" style="background-image: url('BG.jpg'); min-height: 100vh;">

    <div class="bg-gradient-to-br from-gray-800 via-gray-900 to-black shadow-lg rounded-lg p-8 max-w-md w-full text-center relative z-10">
        <h1 class="text-3xl font-semibold text-white mb-6">CineBook</h1>
        <p class="mb-4 text-gray-300">Please select how you want to log in:</p>

        <div class="space-y-4">
            <!-- User Login Button (Just redirects to home.php now) -->
            <button onclick="showUserLogin()" class="w-full py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 transition duration-200">
                Login as User
            </button>
            
            <button onclick="showAdminLogin()" class="w-full py-2 bg-green-500 text-white font-semibold rounded-md hover:bg-green-600 transition duration-200">
                Login as Admin
            </button>
        </div>

        <!-- Admin Login Form -->
        <form action="admin.php" method="GET" id="adminEmailField" class="hidden mt-4">
            <input type="email" name="adminEmail" placeholder="Enter Admin Email" required 
                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 mb-4">
            <button type="submit" class="bg-green-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-green-600 transition duration-300 w-full">
                Submit
            </button>
        </form>
    </div>

</body>
</html>
