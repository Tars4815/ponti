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
    $newPosition = $_POST["newPositionArray"];
    $camPosition = $_POST["camPositionArray"];
    $camTarget = $_POST["camTargetArray"]; 
    $typology = $_POST["typology"];

    // Update data in the annotations table
    $query = "UPDATE annotations SET 
    title = '$newTitle', 
    description = '$newDescription', 
    pos_x = " . explode(',', $newPosition)[0] . ",
    pos_y = " . explode(',', $newPosition)[1] . ",
    pos_z = " . explode(',', $newPosition)[2] . ",
    campos_x = " . explode(',', $camPosition)[0] . ",
    campos_y = " . explode(',', $camPosition)[1] . ",
    campos_z = " . explode(',', $camPosition)[2] . ",
    tarpos_x = " . explode(',', $camTarget)[0] . ",
    tarpos_y = " . explode(',', $camTarget)[1] . ",
    tarpos_z = " . explode(',', $camTarget)[2] . ",
    typology = '$typology'
    WHERE id = $id";

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