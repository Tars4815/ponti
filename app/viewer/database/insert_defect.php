<?php
// insert_defect.php
// This script inserts a defect into the database and returns the inserted ID.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $description = isset($_POST["description"]) ? pg_escape_string($connection, $_POST["description"]) : '';
    $pos_x = isset($_POST["pos_x"]) ? (float)$_POST["pos_x"] : 0.0;
    $pos_y = isset($_POST["pos_y"]) ? (float)$_POST["pos_y"] : 0.0;
    $pos_z = isset($_POST["pos_z"]) ? (float)$_POST["pos_z"] : 0.0;
    $defectType = isset($_POST["defectType"]) ? pg_escape_string($connection, $_POST["defectType"]) : '';
    $severityLev = isset($_POST["severityLev"]) ? (int)$_POST["severityLev"] : 0;
    $structuralEl = isset($_POST["structuralEl"]) ? (int)$_POST["structuralEl"] : null;
    $fkinspection = isset($_POST["fkInspection"]) ? (int)$_POST["fkInspection"] : null;

    error_log("Received defect: description=$description, pos=($pos_x, $pos_y, $pos_z), type=$defectType, severity=$severityLev, structuralEl=$structuralEl, fkinspection=$fkinspection");

    // Get fkElement if structuralEl is defined
    $elementfk = null;
    if (!is_null($structuralEl)) {
        $elementQuery = "SELECT id FROM structural_elements WHERE scalarfield = $1 AND fkStructure = 1";
        $elementResult = pg_query_params($connection, $elementQuery, array($structuralEl));
        
        if (!$elementResult) {
            error_log("Error fetching elementfk: " . pg_last_error($connection));
            echo "Error fetching elementfk: " . pg_last_error($connection);
            exit;
        }

        $elementRow = pg_fetch_assoc($elementResult);
        if ($elementRow) {
            $elementfk = $elementRow['id'];
        } else {
            error_log("No structural element found with scalarfield = $structuralEl");
        }
    }

    // Insert the defect with or without elementfk
    $query = "INSERT INTO defects (x, y, z, type, severity, note, fkElement, fkinspections) 
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8) RETURNING id";

    $result = pg_query_params($connection, $query, array(
        $pos_x, $pos_y, $pos_z, $defectType, $severityLev, $description, $elementfk, $fkinspection
    ));

    if (!$result) {
        error_log("Error inserting defect: " . pg_last_error($connection));
        echo "Error inserting defect: " . pg_last_error($connection);
    } else {
        $insertedRow = pg_fetch_assoc($result);
        echo $insertedRow['id'];
    }
}

pg_close($connection);
?>
