<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            width: 350px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px 0px rgba(0,0,0,0.1);
        }<input type="text" name="phone" autocomplete="off">

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
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        /* Button styles */
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
    </style>
</head>
<body>
    <div class="form-container">
      
        <h2>Student Verification</h2>
        <p>Your form was submitted successfully.</p>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
            $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : "";
            $vc = isset($_REQUEST["vc"]) ? $_REQUEST["vc"] : "";

            // Check if user exists and verify
            $user_found = false;
            $verified = false;
            $user_files = glob('users/*.json');
            foreach ($user_files as $file) {
                $json_data = file_get_contents($file);
                $user_data = json_decode($json_data, true);
                
                if ($user_data["vc"] !== "updated" && $user_data["phone"] === $phone) {
                    $user_found = true;

                    if ($user_data["vc"] === $vc) {
                        $verified = true;

                        // Replace vc with "ok" in JSON data
                        $user_data["vc"] = "ok";
                        $updated_json_data = json_encode($user_data, JSON_PRETTY_PRINT);
                        file_put_contents($file, $updated_json_data);

                        // Move the JSON file to the "verified_users" directory
                        $verified_users_dir = 'verified_users/';
                        $verified_file_name = $verified_users_dir . basename($file);
                        rename($file, $verified_file_name);

                        // Redirect to login.php if phone number and vc are verified
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        // Show HTML form to enter vc code if phone number matches but vc code is incorrect
                        echo "Please enter payment code.<br> <p>Please checkout our library to get payment code.</p>";
                        ?>
                        <form action="" method="post">
                            <input type="hidden" id="phone" name="phone" value="<?php echo $phone; ?>">
                            <label for="vc">Payment Code:</label>
                            <input type="text" id="vc" name="vc" required><br><br>
                            <p>You can go back, your form is submitted!</p>
                            <p>Don't worry! We will call you as soon as possible.</p>
                            <p>Feel free to checkout our library. <a href="https://maps.app.goo.gl/CbxZS19HotHbW4Q69">Here</a></p>
                            <button type="submit">Submit</button>
                        </form>
                        <?php
                        exit;
                    }
                }
            }

            if ($user_found) {
                // Show HTML form to enter phone number if it's found but not verified
                echo "Please enter payment code.<br> <p>Please checkout our library to get payment code.</p>";
                ?>
                <form action="" method="post">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>" required><br><br>
                   
                    <label for="vc">Payment code:</label>
                    <p>Please checkout our library to get payment code.</p>
                    <input type="text" id="vc" name="vc" required><br><br>

                    <button type="submit">Submit</button>
                </form>
                <?php
            } else {
               echo "
    <style>
        #disappearing-paragraph {
            transition: opacity 1s ease;
        }
        .hidden {
            opacity: 0;
        }
    </style>
    <p id=disappearing-paragraph>No such phone number or it is verified.</p>

    <script>
        setTimeout(function() {
            document.getElementById('disappearing-paragraph').classList.add('hidden');
        }, 3000);
    </script>
</html>
";
                ?>
        
                <form action="" method="post">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required><br><br>
                    
                    <button type="submit">Submit</button>
                </form>
                <?php
            }
        } else {
            // Show HTML form to enter phone number if method is not allowed
            ?>
            <form action="" method="post">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required><br><br>
                
                <button type="submit">Submit</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>
