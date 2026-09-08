<?php

header("Content-Type: application/json");

$host = getenv("DB_HOST") ?: "db.vsylfixjilhyecvfytyc.supabase.co";
$port = getenv("DB_PORT") ?: "5432";
$user = getenv("DB_USER") ?: "postgres";
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME") ?: "postgres";

$connString =
    "host=$host " .
    "port=$port " .
    "dbname=$database " .
    "user=$user " .
    "password=$password " .
    "sslmode=require";

$conn = pg_connect($connString);

if (!$conn) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);

    exit;
}