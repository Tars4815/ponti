<?php
// insert_comment.php
// This script inserts a comment into the database and returns the inserted ID.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = pg_escape_string($_POST["title"]);
    $description = pg_escape_string($_POST["description"]);
    $pos_x = (float)$_POST["pos_x"];
    $pos_y = (float)$_POST["pos_y"];
    $pos_z = (float)$_POST["pos_z"];

    // Prepare and execute the query to insert the comment
    $query = "INSERT INTO comments (title, note, x, y, z) VALUES ($1, $2, $3, $4, $5) RETURNING id";    
    $result = pg_query_params($connection, $query, array(
        $title, $description, $pos_x, $pos_y, $pos_z
    ));

    if (!$result) {
        echo "Error inserting data: " . pg_last_error($connection);
    } else {
        // Fetch the inserted ID and echo it in the response
        $insertedRow = pg_fetch_assoc($result);
        $insertedId = $insertedRow['id'];
        echo $insertedId;
    }
}

pg_close($connection);
?>
