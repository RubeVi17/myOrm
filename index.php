<?php

require_once __DIR__.'/Models/User.php';

// Crear
$user = User::create([
    'name'  => 'Rubén',
    'email' => 'ruben@test.com',
    'age'   => 30,
]);

// Leer
//$users = User::where('age', '>', 20)->get();


// Update
//$user->name = 'Rubén v2';
//$user->save();

// Delete
//User::find(1)?->delete();
