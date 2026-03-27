<?php
header('Content-Type: application/json');

// Database connection parameters - Matching index.php configuration
$host = 'localhost';
$db = 'bridges';
$user = 'postgres';
$pass = 'mysecretpassword';

$dsn = "pgsql:host=$host;dbname=$db";
try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Sensors are linked directly to the structure (fkStructure)
$structureId = isset($_GET['structureId']) ? intval($_GET['structureId']) : 1;

// Wrap the column name in double quotes to preserve CamelCase
$sql = 'SELECT id, x, y, z FROM sensors WHERE "fkStructure" = :structureId';

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':structureId', $structureId, PDO::PARAM_INT);
$stmt->execute();

$sensors = [];

// Fetch and format each field to the correct data type
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $sensors[] = [
        'id' => (int)$row['id'],
        'x'  => (float)$row['x'],
        'y'  => (float)$row['y'],
        'z'  => (float)$row['z']
    ];
}

echo json_encode($sensors);
?>