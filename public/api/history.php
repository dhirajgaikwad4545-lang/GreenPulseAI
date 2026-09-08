<?php

header("Content-Type: application/json");

require_once "../config/db.php";

/*
|--------------------------------------------------------------------------
| Get limit
|--------------------------------------------------------------------------
*/

$limit = isset($_GET["limit"])
    ? intval($_GET["limit"])
    : 40;

if ($limit < 1) {
    $limit = 40;
}

if ($limit > 200) {
    $limit = 200;
}


/*
|--------------------------------------------------------------------------
| Fetch historical energy data
|--------------------------------------------------------------------------
|
| We order by created_at so the dashboard uses the actual
| timestamp of the sensor reading.
|
*/

$sql = "
SELECT
    id,
    voltage,
    current,
    temperature,
    power,
    status,
    fault_type,
    ai_confidence,
    created_at
FROM energy_data
ORDER BY created_at DESC
LIMIT $limit
";


$result = pg_query($conn, $sql);


/*
|--------------------------------------------------------------------------
| Database error
|--------------------------------------------------------------------------
*/

if (!$result) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database query failed"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Prepare response
|--------------------------------------------------------------------------
*/

$data = [];

while ($row = pg_fetch_assoc($result)) {

    $data[] = [

        "id" => (int)$row["id"],

        "voltage" => (float)$row["voltage"],

        "current" => (float)$row["current"],

        "temperature" => (float)$row["temperature"],

        "power" => (float)$row["power"],

        "status" => $row["status"],

        "fault_type" => $row["fault_type"],

        "ai_confidence" => (float)$row["ai_confidence"],

        "created_at" => $row["created_at"]

    ];
}


/*
|--------------------------------------------------------------------------
| JSON response
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "count" => count($data),

    "data" => $data

], JSON_UNESCAPED_SLASHES);

?>