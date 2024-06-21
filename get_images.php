<?php

$imagesDirectory = 'images'; // Directory where your images are stored
$imageUrls = [];

// Check if directory exists
if (is_dir($imagesDirectory)) {
    // Open directory
    if ($dh = opendir($imagesDirectory)) {
        // Read files from directory
        while (($file = readdir($dh)) !== false) {
            // Ensure it's a file and not a directory
            if (is_file($imagesDirectory . '/' . $file)) {
                // Add image URL to array
                $imageUrls[] = '/images/' . $file;
              }
        }
        closedir($dh);
    }
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode(['images' => $imageUrls]);
?>