<?php
// insert_structural_element.php
// This script inserts a structural element into the database and returns the inserted ID.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $elementType = isset($_POST["elementType"]) ? pg_escape_string($connection, $_POST["elementType"]) : '';
    $material = isset($_POST["material"]) ? pg_escape_string($connection, $_POST["material"]) : '';
    $inspectionId = isset($_POST["inspectionId"]) ? (int)$_POST["inspectionId"] : 0;

    $px = isset($_POST["px"]) ? floatval($_POST["px"]) : 0;
    $py = isset($_POST["py"]) ? floatval($_POST["py"]) : 0;
    $pz = isset($_POST["pz"]) ? floatval($_POST["pz"]) : 0;
    $sx = isset($_POST["sx"]) ? floatval($_POST["sx"]) : 0;
    $sy = isset($_POST["sy"]) ? floatval($_POST["sy"]) : 0;
    $sz = isset($_POST["sz"]) ? floatval($_POST["sz"]) : 0;
    $rx = isset($_POST["rx"]) ? floatval($_POST["rx"]) : 0;
    $ry = isset($_POST["ry"]) ? floatval($_POST["ry"]) : 0;
    $rz = isset($_POST["rz"]) ? floatval($_POST["rz"]) : 0;

    // Get fkstructure from the inspections table
    $fkstructure = null;
    $structureQuery = "SELECT fkstructure FROM inspections WHERE id = $1";
    $structureResult = pg_query_params($connection, $structureQuery, array($inspectionId));

    if ($structureResult && pg_num_rows($structureResult) > 0) {
        $structureRow = pg_fetch_assoc($structureResult);
        $fkstructure = $structureRow["fkstructure"];
    } else {
        echo "Invalid inspection ID or structure not found.";
        exit;
    }

    $insertQuery = "INSERT INTO structural_elements (
        name, material, fkstructure,
        px, py, pz,
        sx, sy, sz,
        rx, ry, rz
    ) VALUES (
        $1, $2, $3,
        $4, $5, $6,
        $7, $8, $9,
        $10, $11, $12
    ) RETURNING id";

    $insertParams = array(
        $elementType,
        $material,
        $fkstructure,
        $px, $py, $pz,
        $sx, $sy, $sz,
        $rx, $ry, $rz
    );

    $result = pg_query_params($connection, $insertQuery, $insertParams);

    if (!$result) {
        error_log("Error inserting data: " . pg_last_error($connection));
        echo "Error inserting data: " . pg_last_error($connection);
    } else {
        $insertedRow = pg_fetch_assoc($result);
        echo $insertedRow['id'];
    }
}

pg_close($connection);
?>
