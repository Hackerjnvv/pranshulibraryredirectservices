<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fees Layout</title>
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
</head>
<body>
    <div class="container my-4">
        <?php
        // Include the PHP code for authentication and data handling
        $valid_username = 'admin';
        $valid_password = 'password';

        function authenticate() {
            if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
                ($_SERVER['PHP_AUTH_USER'] !== $GLOBALS['valid_username']) ||
                ($_SERVER['PHP_AUTH_PW'] !== $GLOBALS['valid_password'])) {
                header('WWW-Authenticate: Basic realm="Restricted Area"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Authentication required.';
                exit;
            }
        }

        // Check authentication for every page load
        authenticate();

        session_start();

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

