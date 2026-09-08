<?php

require_once __DIR__ . "/config/db.php";

echo json_encode([
    "success" => true,
    "message" => "Supabase database connected successfully!"
]);

?>