<?php
require 'config.php';

if(isset($_SESSION['username'])){
    echo '<script>window.location.href = "./";</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/loginRegister.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form class="registerForm">
            <input id="username" type="text" placeholder="Username" required>
            <input id="email" type="text" placeholder="Email" required>
            <input id="password" type="password" placeholder="Password" required>
            <input id="confirm_password" type="password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <div class="switch">
            Do you have an account? <a href="login.php">Login here</a>
        </div>
        <div class="back-to-youtube">
            <a href="./">Back to YouTube</a>
        </div>
    </div>
</body>

<script src="assets/js/loginRegister.js"></script>
</html>
