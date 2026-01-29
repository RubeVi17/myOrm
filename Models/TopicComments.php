<?php

require_once __DIR__.'/../Core/Model.php';
require_once __DIR__.'/Topic.php';
require_once __DIR__.'/User.php';

class TopicComments extends Model
{
    protected static string $table = 'topics_comments';

    protected array $fillable = [
        'topic_id',
        'user_id',
        'pinned',
        'comment'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function relations(): array
    {
        return [
            'topic' => 'belongsTo',
            'user' => 'belongsTo',
        ];
    }
}