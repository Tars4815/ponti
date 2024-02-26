<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $newTitle = $_POST["newTitle"];
    $newDescription = $_POST["newDescription"];

    // Update data in the annotations table
    $query = "UPDATE annotations SET title = '$newTitle', description = '$newDescription' WHERE id = $id";
    
    $result = pg_query($connection, $query);

    if (!$result) {
        echo "Error updating data: " . pg_last_error($connection);
    }
}

pg_close($connection);
?>
