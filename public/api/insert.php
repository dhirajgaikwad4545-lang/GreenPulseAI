```php
<?php

header("Content-Type: application/json");

require_once "../config/db.php";


/* ============================================================
   API KEY
   ============================================================ */

$EXPECTED_API_KEY =
    getenv("GREENPULSE_API_KEY") ?: "SMART_ENERGY_2026";


/* ============================================================
   SYSTEM THRESHOLDS
   ============================================================ */

/*
   Designed for your approximately 18V nominal
   solar panel system.

   0 - 2V       = IDLE
   2 - 15V      = UNDERVOLTAGE
   15 - 42V     = NORMAL
   > 42V        = OVERVOLTAGE
*/

$IDLE_VOLTAGE = 2.0;

$MIN_NORMAL_VOLTAGE = 15.0;

$MAX_VOLTAGE = 42.0;

$MAX_CURRENT = 2.8;

$MAX_TEMPERATURE = 60.0;


/* ============================================================
   READ JSON
   ============================================================ */

$raw = file_get_contents("php://input");

$input = json_decode($raw, true);


/*
   If JSON is invalid, try normal POST.
*/

if (!is_array($input)) {
    $input = $_POST;
}


/* ============================================================
   API KEY CHECK
   ============================================================ */

$apiKey = trim(
    $input["api_key"] ?? ""
);


if ($apiKey !== $EXPECTED_API_KEY) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Invalid API key"
    ]);

    exit;
}


/* ============================================================
   READ SENSOR VALUES
   ============================================================ */

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


/* ============================================================
   CHECK REQUIRED VALUES
   ============================================================ */

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


/* ============================================================
   VALIDATE NUMBERS
   ============================================================ */

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


/* ============================================================
   PREVENT NEGATIVE READINGS
   ============================================================ */

if ($voltage < 0) {
    $voltage = 0;
}


if ($current < 0) {
    $current = 0;
}


/* ============================================================
   CALCULATE POWER
   ============================================================ */

$power =
    $voltage * $current;


/* ============================================================
   DEFAULT VALUES
   ============================================================ */

$status = "NORMAL";

$fault = "NOMINAL";

$confidence = 95.0;


/* ============================================================
   STATUS + FAULT LOGIC
   ============================================================ */


/*
   ------------------------------------------------------------
   1. IDLE
   ------------------------------------------------------------

   Panel voltage below 2V means essentially
   no solar generation.
*/

if (
    $voltage < $IDLE_VOLTAGE
) {

    $status = "IDLE";

    $fault = "LOW_GENERATION";

    $confidence = 90.0;
}


/*
   ------------------------------------------------------------
   2. OVERVOLTAGE
   ------------------------------------------------------------
*/

elseif (
    $voltage > $MAX_VOLTAGE
) {

    $status = "FAULT";

    $fault = "OVERVOLTAGE";

    $confidence = 98.0;
}


/*
   ------------------------------------------------------------
   3. OVERCURRENT
   ------------------------------------------------------------
*/

elseif (
    $current > $MAX_CURRENT
) {

    $status = "FAULT";

    $fault = "OVERCURRENT";

    $confidence = 97.0;
}


/*
   ------------------------------------------------------------
   4. HIGH TEMPERATURE
   ------------------------------------------------------------
*/

elseif (
    $temperature > $MAX_TEMPERATURE
) {

    $status = "FAULT";

    $fault = "THERMAL_CRITICAL";

    $confidence = 96.0;
}


/*
   ------------------------------------------------------------
   5. UNDERVOLTAGE
   ------------------------------------------------------------

   For your 18V nominal panel system:

       15V and above = normal

       below 15V = undervoltage

   This means:

       16.6V = NORMAL

       14.9V = UNDERVOLTAGE
*/

elseif (
    $voltage < $MIN_NORMAL_VOLTAGE
) {

    $status = "FAULT";

    $fault = "UNDERVOLTAGE";

    $confidence = 94.0;
}


/*
   ------------------------------------------------------------
   6. NORMAL
   ------------------------------------------------------------
*/

else {

    $status = "NORMAL";

    $fault = "NOMINAL";

    $confidence = 95.0;
}


/* ============================================================
   INSERT INTO POSTGRESQL
   ============================================================ */

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


/* ============================================================
   DATABASE ERROR
   ============================================================ */

if (!$result) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database insert failed"
    ]);

    exit;
}


/* ============================================================
   GET INSERTED ID
   ============================================================ */

$row =
    pg_fetch_assoc($result);


/* ============================================================
   SUCCESS RESPONSE
   ============================================================ */

echo json_encode([

    "success" => true,

    "message" =>
        "Data stored successfully",

    "id" =>
        (int)$row["id"],

    "voltage" =>
        round($voltage, 3),

    "current" =>
        round($current, 3),

    "temperature" =>
        round($temperature, 2),

    "power" =>
        round($power, 3),

    "status" =>
        $status,

    "fault" =>
        $fault,

    "ai_confidence" =>
        $confidence

]);

?>