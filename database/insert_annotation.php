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
    $campos_x = (float)$_POST["campos_x"];
    $campos_y = (float)$_POST["campos_y"];
    $campos_z = (float)$_POST["campos_z"];
    $tarpos_x = (float)$_POST["tarpos_x"];
    $tarpos_y = (float)$_POST["tarpos_y"];
    $tarpos_z = (float)$_POST["tarpos_z"];
    $typology = pg_escape_string($_POST["typology"]);

    // Use prepared statements to prevent SQL injection
    $query = "INSERT INTO annotations (title, description, pos_x, pos_y, pos_z, campos_x, campos_y, campos_z, tarpos_x, tarpos_y, tarpos_z, typology) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12) RETURNING id";    
    $result = pg_query_params($connection, $query, array(
        $title, $description, $pos_x, $pos_y, $pos_z,
        $campos_x, $campos_y, $campos_z, $tarpos_x, $tarpos_y, $tarpos_z, $typology
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
