<?php

require_once __DIR__.'/../Core/Model.php';
require_once __DIR__.'/User.php';
require_once __DIR__.'/Categorie.php';
require_once __DIR__.'/TopicComments.php';

class Topic extends Model
{
    protected static string $table = 'topics';

    protected array $fillable = [
        'title',
        'slug',
        'user_id',
        'categorie_id',
        'description',
        'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function comments()
    {
        return $this->hasMany(TopicComments::class, 'topic_id');
    }

    public static function relations(): array
    {
        return [
            'user' => 'belongsTo',
            'categorie' => 'belongsTo',
            'comments' => 'hasMany',
        ];
    }
}