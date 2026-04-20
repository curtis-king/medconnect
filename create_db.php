<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $pdo->exec('CREATE DATABASE IF NOT EXISTS zer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "\n✅ Database 'zer' created successfully!\n";
    exit(0);
} catch (PDOException $e) {
    echo "\n❌ Error: ".$e->getMessage()."\n";
    exit(1);
}
