<?php

require_once __DIR__.'/../Core/Model.php';

class TopicLike extends Model
{
    protected static string $table = 'topics_likes';

    protected array $fillable = [
        'topic_id',
        'user_id',
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