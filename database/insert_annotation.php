<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $pos_x = $_POST["pos_x"];
    $pos_y = $_POST["pos_y"];
    $pos_z = $_POST["pos_z"];
    $campos_x = $_POST["campos_x"];
    $campos_y = $_POST["campos_y"];
    $campos_z = $_POST["campos_z"];
    $tarpos_x = $_POST["tarpos_x"];
    $tarpos_y = $_POST["tarpos_y"];
    $tarpos_z = $_POST["tarpos_z"];

    // Insert data into the annotations table
    $query = "INSERT INTO annotations (title, description, pos_x, pos_y, pos_z, campos_x, campos_y, campos_z, tarpos_x, tarpos_y, tarpos_z) VALUES ('$title', '$description', $pos_x, $pos_y, $pos_z, $campos_x, $campos_y, $campos_z, $tarpos_x, $tarpos_y, $tarpos_z) RETURNING id";    
    $result = pg_query($connection, $query);

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
