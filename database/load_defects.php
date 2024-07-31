<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Fetch existicng annotations from the database
$query = "SELECT * FROM defects ORDER BY id";
$result = pg_query($connection, $query);

$annotations = array();
while ($row = pg_fetch_assoc($result)) {
    $row['x'] = floatval($row['x']);
    $row['y'] = floatval($row['y']);
    $row['z'] = floatval($row['z']);
    $annotations[] = $row;
}

// Close the database connection
pg_close($connection);

// Return the annotations as JSON
echo json_encode($annotations);
?>