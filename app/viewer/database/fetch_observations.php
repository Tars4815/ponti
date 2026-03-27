<?php
header('Content-Type: application/json');
$host = 'localhost'; $db = 'bridges'; $user = 'postgres'; $pass = 'mysecretpassword';
$dsn = "pgsql:host=$host;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $sensorId = isset($_GET['sensorId']) ? intval($_GET['sensorId']) : 0;

    // Use double quotes if your column name is CamelCase in Postgres
    $sql = 'SELECT date, value, uncertainty FROM observations WHERE "fksensor" = :sensorId ORDER BY date ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sensorId', $sensorId, PDO::PARAM_INT);
    $stmt->execute();

    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'date' => $row['date'],
            'value' => (float)$row['value'],
            'uncertainty' => (float)$row['uncertainty'],
        ];
    }
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>