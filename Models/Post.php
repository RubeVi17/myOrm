<?php

require_once __DIR__.'/../Core/Model.php';
require_once __DIR__.'/User.php';

class Post extends Model
{
    protected static string $table = 'posts';


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function relations(): array
    {
        return [
            'user' => 'belongsTo',
        ];
    }
}