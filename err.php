<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .container {
            text-align: center;
        }
        .message {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .error {
            color: #ff0000;
        }
        .valid {
            color: #00ff00;
        }
        .home-link, .go-back-button {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .home-link:hover, .go-back-button:hover {
            background-color: #007bff;
            color: #ffffff;
        }
    </style>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
    <div class="container">
        <?php
        // Check if the 'e' parameter is set in the GET request
        if (isset($_GET['e'])) {
            // Retrieve the value of the 'e' parameter
            $e = $_GET['e'];

            // Check the value of 'e' and respond with different error messages
            switch ($e) {
                case 1:
                    echo "<div class='message error'>Error: Phone number not found.</div>";
                    break;
                case 2:
                    echo "<div class='message error'>Error: Invalid phone number format.</div>";
                    break;
                case 3:
                    echo "<div class='message error'>Error: Phone number is blocked.</div>";
                    break;
                default:
                    echo "<div class='message valid'>Valid number</div>";
                    break;
            }
        } else {
            echo "<a class='home-link' href='index.php'>Home</a>";
        }
        ?>
        <button class="go-back-button" onclick="goBack()">Go Back</button>
    </div>
</body>
</html>
