<?php

require_once 'Core/Database.php';
require_once 'Core/Schema/Blueprint.php';
require_once 'Core/Schema/Schema.php';

$pdo = Database::connection();

$executed = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/Migrations/*.php');
$batch = count($executed) + 1;
$migrations = [];

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
    $migrations[] = $name;

}

if(empty($migrations)){
    echo "No hay migraciones pendientes.\n";
} else {
    echo "Migraciones ejecutadas:\n";
    foreach($migrations as $migration){
        echo "- {$migration}, \n";
    }
}