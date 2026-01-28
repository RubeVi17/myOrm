<?php

require_once 'Core/Database.php';
require_once 'Core/Schema/Blueprint.php';
require_once 'Core/Schema/Schema.php';

$pdo = Database::connection();

$executed = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/Migrations/*.php');
$batch = count($executed) + 1;

foreach($files as $file){

    $name = basename($file, '.php');

    if(in_array($name, $executed)){
        continue;
    }

    echo "Migrando: {$name}\n";

    $migration = require $file;
    $migration->up();

    $stmt = $pdo->prepare(
        "INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)"
    );
    $stmt->execute([':migration' => $name, ':batch' => $batch]);

}

echo "Migraciones completadas.\n";