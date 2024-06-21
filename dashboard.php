<?php
session_start(); // Start the session

// Check if username is available in session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    // If username is not available, redirect to login page
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
    max-width: 100%; /* Adjust as needed */
    margin: 50px auto;
    padding: 0 20px; /* Maintain 20px padding on left and right */
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

table {
    width: 100%; /* Table width set to 100% of its container */
    border-collapse: collapse;
    margin-top: 20px;
}

        h1 {
            text-align: center;
            color: #333;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        p {
            margin-top: 20px;
            font-style: italic;
            font-family: Arial;
        }

        @keyframes colorRotation {
    0% { background-color: rgba(255, 240, 240, 1); } /* Red */
    25% { background-color: rgba(200, 255, 200, 1); } /* Green */
    50% { background-color: rgba(200, 225, 255, 1); } /* Blue */
    75% { background-color: rgba(255, 255, 200, 1); } /* Yellow */
    100% { background-color: rgba(255, 240, 240, 1); } /* Red */
}

.card {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
    margin: 10px;
    width: 200px;
    float: left;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    background-color: rgba(255, 250, 250, 0.2); /* Initial color */
    animation: colorRotation 25s infinite; /* Change duration as per your preference */
}

        .card:hover {
            box-shadow: 0 32px 64px 0 rgba(0,0,0,0.2);
            color: #0000ff;
            width: 200px;
        }

        .container {
            width: 100%;
            overflow: hidden;
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <?php

// Function to get current server time
function getCurrentTime() {
    return date("Y-m-d H:i:s");
}

// Read JSON file
$json_file = 'active_users/' . $username . '.json';

// Check if JSON file exists
if (!file_exists($json_file)) {
    // First time login, create JSON data
    $first_login_data = array(
        "name" => $username,
        "phone" => "NaN",
        "dateofadmission" => getCurrentTime(),
        "mac" => "Null",
        "useableWifi" => "No Network"
    );

    // Encode data as JSON
    $json_data = json_encode(array($first_login_data));

    // Write JSON data to file
    file_put_contents($json_file, $json_data);
} else {
    // Read JSON file contents
    $json_data = file_get_contents($json_file);

    // Decode JSON data into PHP associative array
    $data = json_decode($json_data, true);

    // Check if data is valid
    if ($data === null) {
        echo "Error decoding JSON data";
    } else {
        // Check if the name has been changed
        if ($data[0]['name'] === "User") {
            // Name has not been changed, update admission date
            $data[0]['dateofadmission'] = getCurrentTime();

            // Encode data as JSON
            $json_data = json_encode($data);

            // Write JSON data to file
            file_put_contents($json_file, $json_data);
        }
    }
}
// Read JSON file contents
$json_data = file_get_contents($json_file);

// Decode JSON data into PHP associative array
$data = json_decode($json_data, true);

// Check if data is valid
if ($data === null) {
    echo "Error decoding JSON data";
} else {
    }

?>
        <table>
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Admission Date</th>
                <th>Wi-Fi MAC Address</th>
            </tr>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['dateofadmission']; ?></td>
                    <td><?php echo $row['mac']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p>Your details will be updated within few business hours.</p>

<h2>Avaliable WiFi</h2>
<p>You can currently use <strong><?php echo $row['useableWifi']; ?></strong></p>

<div class="container">
    <div class="card">
        <h3>Pranshu Library 1</h3>
        <p><strong>Password:</strong> pranshulibrary.com</p>
    </div>
    <div class="card">
        <h3>Pranshu Library 2</h3>
        <p><strong>Password:</strong> pranshulibrary.com</p>
    </div>
</div>
<h2>Your Fees Record</h2>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include jsPDF library from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <!-- Include html2canvas library from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media (max-width: 768px) {
            .table-responsive-stack td,
            .table-responsive-stack th {
                display: block;
                width: 100%;
                text-align: right;
            }
            .table-responsive-stack td::before {
                float: left;
                font-weight: bold;
                content: attr(data-title);
            }
            .table-responsive-stack {
                border: none;
            }
            .table-responsive-stack th {
                background: #f8f9fa;
                font-weight: bold;
                padding: 10px;
            }
        }
    </style>

    <div class="container my-4">
        <?php
        // File paths
        $file_path = 'money-4547fe7hd0dhv/money-4547fe7hd0dhv.json';
        $directory_path = 'active_users';

        // Read JSON file
        $json = file_get_contents($file_path);
        $students = json_decode($json, true);

        // Scan directory for .json files and add as students if not already present
        $files = glob($directory_path . '/*.json');
        foreach ($files as $file) {
            $filename = basename($file, '.json');
            $exists = false;
            foreach ($students as $student) {
                if ($student['student_name'] === $filename) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $students[] = [
                    "student_name" => $filename,
                    "fees" => []
                ];
            }
        }

        // Get the selected student index from the URL if present
        $username = $_SESSION['username'];
        $selected_student_index = array_search($username, array_column($students, 'student_name'));

        // Get the selected file from the URL if present
        $selected_file = isset($_GET['file']) ? $_GET['file'] : null;

        if ($selected_file) {
            $file_path = $directory_path . '/' . $selected_file;
            $file_content = file_get_contents($file_path);
            $file_data = json_decode($file_content, true);
        }
        ?>
        
        <?php echo 'Username: ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
        
        <!-- Download PDF button -->
        <button id="download-pdf" class="btn btn-primary mb-3">Download PDF</button>

        <!-- Display the student name -->
        <?php if ($selected_student_index !== false && isset($students[$selected_student_index])): ?>
            <?php $student = $students[$selected_student_index]; ?>
            <div class="student-name mb-3">
                <label for="student-name-<?php echo $selected_student_index; ?>" class="form-label">Student Name: </label>
                <input type="text" id="student-name-<?php echo $selected_student_index; ?>" class="form-control" name="student-name" value="<?php echo htmlspecialchars($student['student_name'], ENT_QUOTES, 'UTF-8'); ?>" readonly>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-responsive-stack">
                    <thead class="thead-light">
                        <tr>
                            <th>MONTH</th>
                            <th>RATE</th>
                            <th>DURATION</th>
                            <th>DUE</th>
                            <th>FINE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($student['fees'] as $fee): ?>
                            <tr>
                                <td data-title="MONTH"><?php echo htmlspecialchars($fee['month'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="RATE"><?php echo htmlspecialchars($fee['rate'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="DURATION"><?php echo htmlspecialchars($fee['duration'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="DUE"><?php echo htmlspecialchars($fee['due'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="FINE"><?php echo htmlspecialchars($fee['fine'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="ACTIONS">Actions Unavailable</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (isset($selected_file) && $file_data): ?>
            <div class="file-editor mb-4">
                <h3>Editing: <?php echo htmlspecialchars($selected_file, ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-responsive-stack">
                        <thead class="thead-light">
                            <tr>
                                <th>KEY</th>
                                <th>VALUE</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($file_data as $key => $value): ?>
                                <tr>
                                    <td data-title="KEY"><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-title="VALUE"><?php echo htmlspecialchars(is_array($value) ? json_encode($value) : $value, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td data-title="ACTIONS">Actions Unavailable</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('download-pdf').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF();

            // Add the logo
            const img = new Image();
            img.src = 'mylogo.png';
            img.onload = function () {
                pdf.addImage(img, 'PNG', 2, 2, 200, 20);

                // Add the content
                html2canvas(document.querySelector('.container')).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = pdf.internal.pageSize.getWidth();
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    pdf.addImage(imgData, 'PNG', 0, 30, imgWidth, imgHeight);

                    // Save the PDF
                    pdf.save('students_fee.pdf');
                });
            };
        });
    </script>


</div>
</body>
</html>