<?php
// get_bridges.php
// This script retrieves bridge data from the database and returns it in GeoJSON format.

// Include the database configuration
// Ensure this file is included to access the database connection parameters
require_once 'config.php';

$connection = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);

if (!$connection) {
    echo "An error occurred.<br>";
    exit;
}

// SQL query to get the GeoJSON data from the PostGIS table
$sql = "
SELECT json_build_object(
    'type', 'FeatureCollection',
    'features', json_agg(ST_AsGeoJSON(t.*)::json)
) AS geojson
FROM (
    SELECT id, name, name_unoff, road_code, municipality, owner, length, spans, image_preview, ST_Transform(geom, 4326) AS geometry
    FROM structures
) t";

$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo $row['geojson'];
