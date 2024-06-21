<?php
session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $vc = $_POST['vc'];
    $password = $_POST['password'];

    // Read JSON files from users directory
    $files = glob('verified_users/*.json');

    foreach ($files as $file) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);

        // Check if phone number, vc, and password match
        if ($data['phone'] === $phone && $data['vc'] === $vc && $data['password'] === $password) {
            // Phone number, vc, and password match, save username in session
            $_SESSION['username'] = $data['name'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        }
    }

    // If no match found, redirect to check_user.php
    header("Location: err.php?e=1");
    exit;
} else {
    // Show initial form to enter phone number, vc, and password
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 400px;
                margin: 50px auto;
                padding: 20px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            form {
                margin-top: 20px;
            }
            input[type="text"],
            input[type="password"],
            input[type="submit"] {
                width: 100%;
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
            input[type="submit"] {
                background-color: #007bff;
                color: #fff;
                cursor: pointer;
            }
            input[type="submit"]:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Login</h2>
            <form action="login.php" method="post">
                Phone Number: <input type="text" name="phone" autocomplete="off">
                <br>
                <div style="display: none;">Verification Code: <input type="text" name="vc" value="ok" readonly><br></div>
                Password: <input type="password" name="password"><br>
                <input type="submit" value="Submit">
            </form>
            <p align="center">Not a member? <a href='index.php'>REGISTER HERE</a>
        </div>
    </body>
    </html>
    <?php
}
?>
