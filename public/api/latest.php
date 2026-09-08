<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/db.php";


/*
|--------------------------------------------------------------------------
| Get Latest Energy Reading
|--------------------------------------------------------------------------
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
LIMIT 1
";


$result = pg_query($conn, $sql);


/*
|--------------------------------------------------------------------------
| Database Error
|--------------------------------------------------------------------------
*/

if (!$result) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database query failed",
        "error" => pg_last_error($conn)
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| No Data
|--------------------------------------------------------------------------
*/

$row = pg_fetch_assoc($result);

if (!$row) {

    echo json_encode([
        "success" => true,
        "data" => null
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Return Latest Data
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "data" => [

        "id" => (int)$row["id"],

        "voltage" => (float)$row["voltage"],

        "current" => (float)$row["current"],

        "temperature" => (float)$row["temperature"],

        "power" => (float)$row["power"],

        "status" => $row["status"],

        "fault_type" => $row["fault_type"],

        "ai_confidence" => (float)$row["ai_confidence"],

        "created_at" => $row["created_at"]

    ]

], JSON_UNESCAPED_SLASHES);

?>