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
    $newPositionArray = explode(',', $_POST["newPositionArray"]);

    // Update data in the defects table using parameterized query to prevent SQL injection
    $query = "UPDATE comments SET 
        title = $1, 
        note = $2, 
        x = $3,
        y = $4,
        z = $5
        WHERE id = $6";

    $result = pg_query_params($connection, $query, array(
        $newTitle,
        $newDescription,
        $newPositionArray[0],
        $newPositionArray[1],
        $newPositionArray[2],
        $id
    ));

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