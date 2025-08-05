<?php
// fetch_oriented_images.php
// This script fetches oriented images from the database and returns them as JSON.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Check if the request method is GET and if the inspectionId parameter is provided
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["inspectionId"])) {
    $inspectionId = (int) $_GET["inspectionId"];
    // Prepare and execute the query to fetch oriented images for the given inspectionId
    $query = "SELECT filename, x, y, z, omega, phi, kappa
              FROM oriented_images
              WHERE fkinspection = $1";
    $result = pg_query_params($connection, $query, array($inspectionId));

    if (!$result) {
        http_response_code(500);
        echo json_encode(["error" => pg_last_error($connection)]);
        exit;
    }

    $images = [];
    while ($row = pg_fetch_assoc($result)) {
        $images[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($images);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing or invalid inspectionId"]);
}

pg_close($connection);
?>
