<?php

require_once __DIR__.'/../Core/Model.php';
require_once __DIR__.'/Topic.php';
class Categorie extends Model
{
    protected static string $table = 'categories';

    protected array $fillable = [
        'name',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public static function relations(): array
    {
        return [
            'topics' => 'hasMany',
        ];
    }

    public static function getTableName(): string
    {
        return self::$table;
    }
}