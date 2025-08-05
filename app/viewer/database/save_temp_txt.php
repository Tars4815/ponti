<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['content']) || !isset($input['filename'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$tempDir = '../temp/'; // Make sure this directory exists and is writable
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}

$filepath = $tempDir . $input['filename'];
file_put_contents($filepath, $input['content']);

echo json_encode(['filepath' => './temp/' . $input['filename']]);
?>