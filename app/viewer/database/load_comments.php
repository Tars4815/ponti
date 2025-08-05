<?php
// load_comments.php
// This script loads existing comments from the database and returns them as JSON.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Fetch existicng annotations from the database
$query = "SELECT * FROM comments ORDER BY id";
$result = pg_query($connection, $query);

$annotations = array();
while ($row = pg_fetch_assoc($result)) {
    $row['x'] = floatval($row['x']);
    $row['y'] = floatval($row['y']);
    $row['z'] = floatval($row['z']);
    $annotations[] = $row;
}

// Close the database connection
pg_close($connection);

// Return the annotations as JSON
echo json_encode($annotations);
?>