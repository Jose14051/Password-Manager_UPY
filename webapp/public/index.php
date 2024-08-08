<?php include './components/authenticate.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a1a, #4a0072); /* Background gradient */
            color: #ffffff; /* White text */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: rgba(0, 0, 0, 0.8); /* Dark navbar background */
        }
        .navbar-brand {
            color: #ffffff !important; /* White brand color */
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            color: #ffffff !important; /* White link color */
        }
        .navbar-nav .nav-link:hover {
            color: #6f42c1 !important; /* Purple on hover */
        }
        .container {
            background: rgba(0, 0, 0, 0.7); /* Dark semi-transparent background */
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            margin-top: 2rem;
        }
        .container h2 {
            color: #ffffff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        .container p {
            color: #dddddd; /* Light grey text */
            line-height: 1.6;
        }
        footer {
            background: #1a1a1a;
            color: #ffffff;
            padding: 1rem 0;
        }
        footer span {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<?php include './components/nav-bar.php'; ?>

<!-- Main Content Area -->
<div class="container">
    <h2>Welcome to the Cybersecurity Password Manager Project!</h2>
    <p>Congratulations on getting your Docker-based web application up and running! You’re a machine, you’re a badass, it's done. Click Home and start managing your passwords.</p>
</div>

<!-- Footer -->
<footer class="footer mt-5">
    <div class="container text-center">
        <span>&copy; 2024 Asset Tracker. All rights reserved.</span>
    </div>
</footer>

<!-- Bootstrap JS and other scripts -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<!-- Add additional scripts as needed -->

</body>
</html>
