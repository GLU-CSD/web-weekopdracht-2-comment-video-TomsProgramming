<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/loginRegister.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form class="loginForm">
            <input type="text" id="username" placeholder="Username" required>
            <input type="password" id="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="switch">
            Don’t have an account? <a href="register.php">Register here</a>
        </div>
        <div class="back-to-youtube">
        <a href="./">Back to YouTube</a>
        </div>
    </div>
</body>

<script src="assets/js/loginRegister.js"></script>
</html>
