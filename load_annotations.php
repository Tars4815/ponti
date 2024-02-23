<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Fetch existing annotations from the database
$query = "SELECT * FROM annotations ORDER BY id";
$result = pg_query($connection, $query);

$annotations = array();
while ($row = pg_fetch_assoc($result)) {
    $row['pos_x'] = floatval($row['pos_x']);
    $row['pos_y'] = floatval($row['pos_y']);
    $row['pos_z'] = floatval($row['pos_z']);
    $row['campos_x'] = floatval($row['campos_x']);
    $row['campos_y'] = floatval($row['campos_y']);
    $row['campos_z'] = floatval($row['campos_z']);
    $row['tarpos_x'] = floatval($row['tarpos_x']);
    $row['tarpos_y'] = floatval($row['tarpos_y']);
    $row['tarpos_z'] = floatval($row['tarpos_z']);
    $annotations[] = $row;
}

// Close the database connection
pg_close($connection);

// Return the annotations as JSON
echo json_encode($annotations);
?>