<?php

$servername = "mysql-database";
$username = "user";
$password = "supersecretpw";
$dbname = "password_manager";

$conn = new mysqli($servername, $username, $password, $dbname);

unset($error_message);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        setcookie('authenticated', $username, time() + 3600, '/');
        header("Location: index.php");
        exit();
    } else {
        $error_message = 'Invalid username or password.';
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Login Page</title>
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a, #4a0072); /* Gradient background */
            color: #ffffff; /* White text */
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: rgba(0, 0, 0, 0.8); /* Semi-transparent background */
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px); /* Blur effect */
        }
        .btn-primary {
            background: linear-gradient(135deg, #6f42c1, #4a0072); /* Gradient button */
            border: none; /* Remove default border */
            transition: background 0.3s ease, transform 0.3s ease; /* Smooth transition */
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4a0072, #6f42c1); /* Hover gradient */
            transform: scale(1.05); /* Slight zoom effect */
        }
        .alert-danger {
            background: #c62828; /* Error background */
            color: #ffffff; /* White text */
            border: 1px solid #b71c1c; /* Error border */
            border-radius: 0.25rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .form-control {
            background: #333333; /* Dark gray input background */
            color: #ffffff; /* White text in inputs */
            border: 1px solid #444444; /* Dark gray border */
            border-radius: 0.25rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
        }
        .form-control:focus {
            border-color: #6f42c1; /* Purple border on focus */
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.5); /* Purple shadow on focus */
            background: #222222; /* Darker background on focus */
        }
        .text-center {
            font-weight: bold; /* Bold text */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5); /* Text shadow for effect */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center">Login</h2>
            <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
