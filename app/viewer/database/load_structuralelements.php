<?php
// load_structuralelements.php
// This script loads existing structural element groups from the database and returns them as JSON.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Fetch existing element groups from db
$query = "SELECT name, COUNT(*) FROM structural_elements GROUP BY name";
$result = pg_query($connection, $query);

$elementGroups = array();
while ($row = pg_fetch_assoc($result)) {
    $elementGroups[] = $row;
}

// Close the database connection
pg_close($connection);

// Return the annotations as JSON
echo json_encode($elementGroups);
?>