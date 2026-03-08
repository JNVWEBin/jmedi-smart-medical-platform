<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_url = getenv('DATABASE_URL');
if ($db_url) {
    $parsed = parse_url($db_url);
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? 5432;
    $dbname = ltrim($parsed['path'] ?? '', '/');
    $user = $parsed['user'] ?? '';
    $pass = $parsed['pass'] ?? '';
} else {
    $host = getenv('PGHOST') ?: 'localhost';
    $port = getenv('PGPORT') ?: 5432;
    $dbname = getenv('PGDATABASE') ?: 'postgres';
    $user = getenv('PGUSER') ?: 'postgres';
    $pass = getenv('PGPASSWORD') ?: '';
}

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
