<?php
// search_elementgroup.php
// This script searches for a specific structural element group in the database and returns it as JSON.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

$group = $_GET['group'] ?? '';

if ($group) {
    // Prepare and execute your query to search for the group
    $result = pg_query_params($connection, "SELECT * FROM structural_elements WHERE name = $1", array($group));

    if (!$result) {
        echo json_encode(["error" => "An error occurred while executing the query."]);
    } else {
        $data = pg_fetch_all($result);
        echo json_encode($data);
    }
} else {
    echo json_encode(['error' => 'No group specified']);
}

// Close the database connection
pg_close($connection);
?>
