<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// get_inspection_info.php
// This script retrieves information about inspections from the database and returns counts of related data.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// Input validation
$inspectionId = isset($_GET['inspectionId']) ? intval($_GET['inspectionId']) : 0;
if ($inspectionId <= 0) {
    echo json_encode(['error' => 'Invalid inspection ID']);
    exit();
}

// Count oriented images
$stmt = $pdo->prepare("SELECT COUNT(*) FROM oriented_images WHERE fkinspection = :id");
$stmt->bindParam(':id', $inspectionId, PDO::PARAM_INT);
$stmt->execute();
$orientedCount = (int) $stmt->fetchColumn();

// Count pointclouds
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pointclouds WHERE fkinspection = :id");
$stmt->bindParam(':id', $inspectionId, PDO::PARAM_INT);
$stmt->execute();
$pointcloudCount = (int) $stmt->fetchColumn();

// Count defects
$stmt = $pdo->prepare("SELECT COUNT(*) FROM defects WHERE fkinspections = :id");
$stmt->bindParam(':id', $inspectionId, PDO::PARAM_INT);
$stmt->execute();
$defectCount = (int) $stmt->fetchColumn();

// Return JSON
echo json_encode([
    'counts' => [
        'oriented_images' => $orientedCount,
        'pointclouds' => $pointcloudCount,
        'defects' => $defectCount
    ]
]);
