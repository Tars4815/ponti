<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = pg_escape_string($_POST["title"]);
    $description = pg_escape_string($_POST["description"]);
    $pos_x = (float)$_POST["pos_x"];
    $pos_y = (float)$_POST["pos_y"];
    $pos_z = (float)$_POST["pos_z"];
    $defectType = pg_escape_string($_POST["defectType"]);
    $severityLev = pg_escape_string($_POST["severityLev"]);

    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO defects (title, note, x, y, z, typology, severity) VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id";    
    $result = pg_query_params($connection, $query, array(
        $title, $description, $pos_x, $pos_y, $pos_z, $defectType, $severityLev
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
