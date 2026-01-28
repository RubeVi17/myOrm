<?php

require_once __DIR__.'/Models/Topic.php';

// Crear

/*
$topic = Topic::create([
    'title' => 'Introducción a PHP ORM',
    'slug' => 'introduccion-a-php-orm',
    'user_id' => 4,
    'description' => 'Este tema cubre los conceptos básicos de un ORM en PHP.'
]);
*/

$topic = Topic::find(1);
var_dump($topic);