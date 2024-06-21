<?php
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'add_student') {
            $new_student = [
                "student_name" => $_POST['student_name'],
                "fees" => []
            ];
            $students[] = $new_student;
        } elseif (isset($_POST['student_index'])) {
            $student_index = $_POST['student_index'];
            if ($action == 'add') {
                $new_fee = [
                    "month" => $_POST['month'],
                    "rate" => $_POST['rate'],
                    "duration" => $_POST['duration'],
                    "due" => $_POST['due'],
                    "fine" => $_POST['fine']
                ];
                $students[$student_index]['fees'][] = $new_fee;
            } elseif ($action == 'delete') {
                $fee_index = $_POST['fee_index'];
                array_splice($students[$student_index]['fees'], $fee_index, 1);
            } elseif ($action == 'edit') {
                $fee_index = $_POST['fee_index'];
                $students[$student_index]['fees'][$fee_index] = [
                    "month" => $_POST['month'],
                    "rate" => $_POST['rate'],
                    "duration" => $_POST['duration'],
                    "due" => $_POST['due'],
                    "fine" => $_POST['fine']
                ];
            }
        } elseif ($action == 'add_file_entry') {
            $file_name = $_POST['file_name'];
            $new_key = $_POST['new_key'];
            $new_value = $_POST['new_value'];

            $file_path = $directory_path . '/' . $file_name;
            $file_content = file_get_contents($file_path);
            $file_data = json_decode($file_content, true);

            $file_data[$new_key] = $new_value;

            file_put_contents($file_path, json_encode($file_data, JSON_PRETTY_PRINT));
            header('Location: ' . $_SERVER['PHP_SELF'] . '?file=' . $file_name);
            exit;
        } elseif ($action == 'edit_file_entry') {
            $file_name = $_POST['file_name'];
            $entry_key = $_POST['entry_key'];
            $entry_value = $_POST['entry_value'];

            $file_path = $directory_path . '/' . $file_name;
            $file_content = file_get_contents($file_path);
            $file_data = json_decode($file_content, true);

            $file_data[$entry_key] = $entry_value;

            file_put_contents($file_path, json_encode($file_data, JSON_PRETTY_PRINT));
            header('Location: ' . $_SERVER['PHP_SELF'] . '?file=' . $file_name);
            exit;
        } elseif ($action == 'delete_file_entry') {
            $file_name = $_POST['file_name'];
            $entry_key = $_POST['entry_key'];

            $file_path = $directory_path . '/' . $file_name;
            $file_content = file_get_contents($file_path);
            $file_data = json_decode($file_content, true);

            unset($file_data[$entry_key]);

            file_put_contents($file_path, json_encode($file_data, JSON_PRETTY_PRINT));
            header('Location: ' . $_SERVER['PHP_SELF'] . '?file=' . $file_name);
            exit;
        }

        // Write updated data back to JSON file
        file_put_contents($file_path, json_encode($students, JSON_PRETTY_PRINT));
        // Redirect to avoid resubmission on refresh
        header('Location: ' . $_SERVER['PHP_SELF'] . (isset($student_index) ? '?student=' . $student_index : ''));
        exit;
    }
}

// Get the selected student index from the URL if present
$selected_student_index = isset($_GET['student']) ? intval($_GET['student']) : 0;

// Get the selected file from the URL if present
$selected_file = isset($_GET['file']) ? $_GET['file'] : null;

if ($selected_file) {
    $file_path = $directory_path . '/' . $selected_file;
    $file_content = file_get_contents($file_path);
    $file_data = json_decode($file_content, true);
}
?>
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
        <!-- Form to add a new student -->
        <div class="form-group">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addStudentModal">Add New Student</button>
        </div>

        <!-- Modal for adding a new student -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="action" value="add_student">
                            <div class="form-group">
                                <label for="student_name">Student Name</label>
                                <input type="text" class="form-control" id="student_name" name="student_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Student</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dropdown menu to select student -->
        <div class="form-group">
            <label for="student-select">Select Student:</label>
            <select id="student-select" class="form-control" onchange="navigateToStudent(this.value)">
                <?php foreach ($students as $index => $student): ?>
                    <option value="<?php echo $index; ?>" <?php echo $selected_student_index === $index ? 'selected' : ''; ?>>
                        <?php echo $student['student_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Dropdown menu to select file -->
        
        <?php if (isset($students[$selected_student_index])): ?>
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
                        <?php foreach ($student['fees'] as $fee_index => $fee): ?>
                            <tr>
                                <td data-title="MONTH"><?php echo htmlspecialchars($fee['month'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="RATE"><?php echo htmlspecialchars($fee['rate'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="DURATION"><?php echo htmlspecialchars($fee['duration'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="DUE"><?php echo htmlspecialchars($fee['due'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="FINE"><?php echo htmlspecialchars($fee['fine'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td data-title="ACTIONS">
                                    <form method="post" class="d-inline-block">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="student_index" value="<?php echo $selected_student_index; ?>">
                                        <input type="hidden" name="fee_index" value="<?php echo $fee_index; ?>">
                                        <input type="text" name="month" value="<?php echo htmlspecialchars($fee['month'], ENT_QUOTES, 'UTF-8'); ?>" required class="form-control mb-2">
                                        <input type="text" name="rate" value="<?php echo htmlspecialchars($fee['rate'], ENT_QUOTES, 'UTF-8'); ?>" required class="form-control mb-2">
                                        <input type="text" name="duration" value="<?php echo htmlspecialchars($fee['duration'], ENT_QUOTES, 'UTF-8'); ?>" required class="form-control mb-2">
                                        <input type="text" name="due" value="<?php echo htmlspecialchars($fee['due'], ENT_QUOTES, 'UTF-8'); ?>" required class="form-control mb-2">
                                        <input type="text" name="fine" value="<?php echo htmlspecialchars($fee['fine'], ENT_QUOTES, 'UTF-8'); ?>" required class="form-control mb-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </form>
                                    <form method="post" class="d-inline-block">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="student_index" value="<?php echo $selected_student_index; ?>">
                                        <input type="hidden" name="fee_index" value="<?php echo $fee_index; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <form method="post" class="mb-4">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="student_index" value="<?php echo $selected_student_index; ?>">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <input type="text" name="month" placeholder="Month" required class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" name="rate" placeholder="Rate" required class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" name="duration" placeholder="Duration" required class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" name="due" placeholder="Due Date" required class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" name="fine" placeholder="Fine" required class="form-control">
                    </div>
                    <div class="form-group col-md-1">
                        <button type="submit" class="btn btn-success btn-block">Add</button>
                    </div>
                </div>
            </form>
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
                                    <td data-title="ACTIONS">
                                        <form method="post" class="d-inline-block">
                                            <input type="hidden" name="action" value="edit_file_entry">
                                            <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($selected_file, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="entry_key" value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="text" name="entry_value" value="<?php echo htmlspecialchars(is_array($value) ? json_encode($value) : $value, ENT_QUOTES, 'UTF-8'); ?>" required class="form-control mb-2">
                                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                        </form>
                                        <form method="post" class="d-inline-block">
                                            <input type="hidden" name="action" value="delete_file_entry">
                                            <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($selected_file, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="entry_key" value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <form method="post" class="mb-4">
                    <input type="hidden" name="action" value="add_file_entry">
                    <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($selected_file, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <input type="text" name="new_key" placeholder="New Key" required class="form-control">
                        </div>
                        <div class="form-group col-md-5">
                            <input type="text" name="new_value" placeholder="New Value" required class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-success btn-block">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function navigateToStudent(studentIndex) {
            window.location.href = '?student=' + studentIndex;
        }

        function navigateToFile(fileName) {
            window.location.href = '?file=' + fileName;
        }
    </script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON Viewer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h1>Student Dashboard Edit</h1>

    <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
            <label for="jsonFile">Select Student:</label>
            <select class="form-control" id="jsonFile" name="jsonFile">
                <?php
                $directory = 'active_users/';
                $files = glob($directory . '*.json');
                
                if ($files) {
                    foreach ($files as $file) {
                        $filename = basename($file);
                        echo "<option value=\"$filename\" " . (isset($_GET['jsonFile']) && $_GET['jsonFile'] === $filename ? 'selected' : '') . ">$filename</option>";
                    }
                } else {
                    echo '<option value="">No JSON files available</option>';
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">NEXT</button>
    </form>

    <hr>

    <div id="jsonTable">
    <?php
    if (isset($_GET['jsonFile'])) {
        $selectedFile = $_GET['jsonFile'];
        $filePath = $directory . $selectedFile;

        if (file_exists($filePath)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Save the edited data
                $jsonData = json_decode(file_get_contents($filePath), true);

                foreach ($_POST['data'] as $index => $item) {
                    foreach ($item as $key => $value) {
                        $jsonData[$index][$key] = $value;
                    }
                }

                file_put_contents($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));
                echo '<div class="alert alert-success" role="alert">JSON data saved successfully.</div>';
            }

            // Display the JSON data in a form
            $jsonContents = file_get_contents($filePath);
            $jsonData = json_decode($jsonContents, true);

            if ($jsonData !== null && is_array($jsonData) && !empty($jsonData)) {
                echo '<h2>' . htmlspecialchars($selectedFile) . '</h2>';
                echo '<form method="POST" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?jsonFile=' . htmlspecialchars($selectedFile) . '">';
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-striped">';
                echo '<thead class="thead-dark"><tr>';

                // Extract headers from the first item in the array
                $firstItem = reset($jsonData);
                if (is_array($firstItem)) {
                    $headers = array_keys($firstItem);
                    foreach ($headers as $header) {
                        echo '<th>' . htmlspecialchars($header) . '</th>';
                    }

                    echo '</tr></thead>';
                    echo '<tbody>';

                    foreach ($jsonData as $index => $item) {
                        echo '<tr>';
                        foreach ($headers as $header) {
                            echo '<td><input type="text" class="form-control" name="data[' . $index . '][' . htmlspecialchars($header) . ']" value="' . htmlspecialchars($item[$header] ?? '') . '"></td>';
                        }
                        echo '</tr>';
                    }

                    echo '</tbody>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Invalid JSON structure.</div>';
                }
                
                echo '</table>';
                echo '</div>';
                echo '<button type="submit" class="btn btn-success">Save Changes</button>';
                echo '</form>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Invalid or empty JSON format in the selected file.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Selected file does not exist.</div>';
        }
    }
    ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
