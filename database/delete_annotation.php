<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    // Delete the record from the annotations table
    $query = "DELETE FROM annotations WHERE id = $id";
    $result = pg_query($connection, $query);

    if (!$result) {
        echo "Error deleting data: " . pg_last_error($connection);
    } else {
        echo "Annotation deleted successfully";
    }
}

pg_close($connection);
?>
