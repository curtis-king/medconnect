<?php

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=zer;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $stmt = $db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'zer'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (in_array('personal_access_tokens', $tables)) {
        echo "✅ Sanctum token table EXISTS\n";
    } else {
        echo "❌ Sanctum token table MISSING\n";
        echo 'Available tables: '.implode(', ', array_slice($tables, 0, 5))."...\n";
    }
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage();
}
