<?php

require_once __DIR__.'/Models/Post.php';

// Crear

$post = Post::create([
    'user_id' => 4,
    'title'   => 'Mi primer post',
    'body'    => 'Contenido del post...',
]);