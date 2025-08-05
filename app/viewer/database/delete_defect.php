<?php
// delete_defect.php
// This script deletes a defect from the database based on the provided ID.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    // Delete the record from the annotations table
    $query = "DELETE FROM defects WHERE id = $id";
    $result = pg_query($connection, $query);

    if (!$result) {
        echo "Error deleting data: " . pg_last_error($connection);
    } else {
        echo "Annotation deleted successfully";
    }
}

pg_close($connection);
?>
