<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/db.php";


/*
|--------------------------------------------------------------------------
| Summary Query
|--------------------------------------------------------------------------
*/

$sql = "
SELECT

    COUNT(*) AS total_readings,

    COALESCE(AVG(voltage), 0) AS avg_voltage,

    COALESCE(AVG(current), 0) AS avg_current,

    COALESCE(AVG(temperature), 0) AS avg_temperature,

    COALESCE(AVG(power), 0) AS avg_power,

    COALESCE(MAX(power), 0) AS peak_power,

    COALESCE(
        SUM(
            CASE
                WHEN status = 'FAULT'
                THEN 1
                ELSE 0
            END
        ),
        0
    ) AS fault_count

FROM energy_data
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
| Get Result
|--------------------------------------------------------------------------
*/

$row = pg_fetch_assoc($result);


/*
|--------------------------------------------------------------------------
| Return Summary
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "data" => [

        "total_readings" =>
            (int)($row["total_readings"] ?? 0),

        "avg_voltage" =>
            round((float)($row["avg_voltage"] ?? 0), 3),

        "avg_current" =>
            round((float)($row["avg_current"] ?? 0), 3),

        "avg_temperature" =>
            round((float)($row["avg_temperature"] ?? 0), 2),

        "avg_power" =>
            round((float)($row["avg_power"] ?? 0), 3),

        "peak_power" =>
            round((float)($row["peak_power"] ?? 0), 3),

        "fault_count" =>
            (int)($row["fault_count"] ?? 0)

    ]

], JSON_UNESCAPED_SLASHES);

?>