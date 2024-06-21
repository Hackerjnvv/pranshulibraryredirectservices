<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>
    <link rel="stylesheet" href="aboutus/styles.css">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #555555;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            margin-right: 10px;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
        }
        .about-us {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .form-container {
            width: 350px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px 0px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        label {
            color: #555;
            font-size: 16px;
            font-weight: bold;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            resize: vertical;
            max-height: 120px;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        button[type="submit"]:focus {
            outline: none;
        }
        #charCountContainer {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="aboutus/logo.webp" width="60px" alt="Logo">
            <span class="logo-text">Pranshu Library</span>
        </div>
        <ul class="nav-links">
            <li><a style="color: rgb(179, 179, 179);" href="/login.php">Log In</a></li>
            <li><a style="color: rgb(255, 255, 255);" href="#">Contact</a></li>
        </ul>
    </div>
    <section class="about-us">
        <div class="form-container">
            <h2>Student Registration Form</h2>
            <form action="save_user.php" method="post" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="password">Create Password: In Digits</label>
                <input type="text" id="password" name="password" placeholder="Used for login" required pattern="\d*">

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required>
                <label for="address">Address:</label>
                <textarea id="address" name="address" required></textarea>
                <div id="charCountContainer">
                    <p id="charCount" style="color: black;"></p>
                    <p id="remainingChars" style="color: black;"></p>
                </div>
                <button type="submit">Submit</button>
            </form>
            <p align="center" style="color: black;">Already a member? <a href="login.php">LOGIN HERE</a></p>
        </div>
    </section>
    <footer>
        <p>&copy; 2024 Pranshu Library. All rights reserved.</p>
    </footer>
    <script>
        document.getElementById('address').addEventListener('input', function() {
            var text = this.value;
            var charCount = text.length;
            var charLimit = 200;
            var remainingChars = charLimit - charCount;

            if (remainingChars < 0) {
                this.value = text.slice(0, charLimit);
                remainingChars = 0;
            }

            document.getElementById('charCount').textContent = charCount + '/' + charLimit + ' characters, ';
            document.getElementById('remainingChars').textContent = remainingChars + ' characters remaining';
        });
    </script>
</body>
</html>
