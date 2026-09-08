<?php

header("Content-Type: application/json");

require_once "../config/db.php";

$EXPECTED_API_KEY =
    getenv("GREENPULSE_API_KEY") ?: "SMART_ENERGY_2026";


/* Read JSON */
$raw = file_get_contents("php://input");

$input = json_decode($raw, true);

if (!is_array($input)) {
    $input = $_POST;
}


/* Check API key */
$apiKey = trim($input["api_key"] ?? "");

if ($apiKey !== $EXPECTED_API_KEY) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Invalid API key"
    ]);

    exit;
}


/* Read sensor values */
$voltage =
    isset($input["voltage"])
    ? (float)$input["voltage"]
    : null;

$current =
    isset($input["current"])
    ? (float)$input["current"]
    : null;

$temperature =
    isset($input["temperature"])
    ? (float)$input["temperature"]
    : null;


/* Check values */
if (
    $voltage === null ||
    $current === null ||
    $temperature === null
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Sensor values required"
    ]);

    exit;
}


/* Validate numbers */
if (
    !is_finite($voltage) ||
    !is_finite($current) ||
    !is_finite($temperature)
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid sensor values"
    ]);

    exit;
}


/* Prevent negative readings */
if ($voltage < 0) {
    $voltage = 0;
}

if ($current < 0) {
    $current = 0;
}


/* Calculate power */
$power = $voltage * $current;


/* Fault detection */

$status = "NORMAL";
$fault = "NOMINAL";
$confidence = 95.0;


if ($voltage > 42.0) {

    $status = "FAULT";
    $fault = "OVERVOLTAGE";
    $confidence = 98.0;

}

elseif ($current > 2.8) {

    $status = "FAULT";
    $fault = "OVERCURRENT";
    $confidence = 97.0;

}

elseif ($temperature > 60.0) {

    $status = "FAULT";
    $fault = "THERMAL CRITICAL";
    $confidence = 96.0;

}

elseif ($voltage < 2.0) {

    $status = "IDLE";
    $fault = "LOW GENERATION";
    $confidence = 90.0;

}

elseif (
    $voltage < 10.0 &&
    $current > 0.1
) {

    $status = "FAULT";
    $fault = "UNDERVOLTAGE / SHADING";
    $confidence = 94.0;
}


/* Insert into PostgreSQL */

$sql = "
INSERT INTO energy_data
(
    voltage,
    current,
    temperature,
    power,
    status,
    fault_type,
    ai_confidence
)
VALUES
(
    $1,
    $2,
    $3,
    $4,
    $5,
    $6,
    $7
)
RETURNING id
";


$result = pg_query_params(
    $conn,
    $sql,
    [
        $voltage,
        $current,
        $temperature,
        $power,
        $status,
        $fault,
        $confidence
    ]
);


/* Database error */
if (!$result) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database insert failed"
    ]);

    exit;
}


/* Get inserted ID */
$row = pg_fetch_assoc($result);


/* Success response */
echo json_encode([

    "success" => true,

    "message" => "Data stored successfully",

    "id" => (int)$row["id"],

    "voltage" => round($voltage, 3),

    "current" => round($current, 3),

    "temperature" => round($temperature, 2),

    "power" => round($power, 3),

    "status" => $status,

    "fault" => $fault,

    "ai_confidence" => $confidence

]);

?>