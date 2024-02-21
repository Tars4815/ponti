<?php
$connection = pg_connect("host=localhost port=5432 dbname=bridges user=postgres password=root");

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Fetch existing annotations from the database
$query = "SELECT * FROM annotations";
$result = pg_query($connection, $query);

$annotations = array();
while ($row = pg_fetch_assoc($result)) {
    $annotations[] = $row;
}

// Close the database connection
pg_close($connection);

// Return the annotations as JSON
echo json_encode($annotations);
?>
