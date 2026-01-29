<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Builder.php';

abstract class Model
{
    /**
     * Nombre de la tabla (definido en el modelo hijo)
     */
    protected static string $table;

    /**
     * Atributos del modelo (columnas)
     */
    protected array $attributes = [];

    /**
     * Indica si el registro ya existe en BD
     */
    protected bool $exists = false;

    protected array $relations = [];

    protected array $fillable = [];

    protected bool $timestamps = true;

    protected ?string $lastSql = null;

    /**
     * Primary key (hardcodeada por ahora)
     */
    protected string $primaryKey = 'id';

    /**
     * Constructor → hidrata el modelo
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;

        if (isset($attributes[$this->primaryKey])) {
            $this->exists = true;
        }
    }

    public static function query(): Builder
    {
        return new Builder(static::$table, static::class);
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function find($id): ?static
    {
        return static::query()
            ->where((new static)->primaryKey, $id)
            ->first();
    }

    public static function where($column, $operator, $value = null): Builder
    {
        return static::query()->where($column, $operator, $value);
    }

    /* =====================================================
     |  Creación y persistencia
     ===================================================== */

    public static function create(array $attributes)
    {
        $model = new static();

        if(!empty($model->fillable)){
            $attributes = array_intersect_key(
                $attributes,
                array_flip($model->fillable)
            );
        }

        foreach ($attributes as $key => $value) {
            $model->$key = $value;
        }

        if($model->timestamps) {
            $now = date('Y-m-d H:i:s');
            $attributes['created_at'] ??= $now;
            $attributes['updated_at'] ??= $now;
        }

        $model->save();
        return $model;
    }

    public function save(): bool
    {
        $pdo = Database::connection();

        if ($this->exists) {
            // UPDATE
            $sets = [];
            $values = [];

            foreach ($this->attributes as $key => $value) {
                if ($key === $this->primaryKey) continue;

                $sets[] = "{$key} = ?";
                $values[] = $value;
            }

            $values[] = $this->attributes[$this->primaryKey];

            $sql = "UPDATE " . static::$table .
                   " SET " . implode(', ', $sets) .
                   " WHERE {$this->primaryKey} = ?";

            $stmt = $pdo->prepare($sql);
            return $stmt->execute($values);
        }

        // INSERT
        $columns = array_keys($this->attributes);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO " . static::$table .
               " (" . implode(',', $columns) . ")" .
               " VALUES (" . implode(',', $placeholders) . ")";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array_values($this->attributes));

        $this->lastSql = $result ? $sql : null;
        if ($result) {
            $this->attributes[$this->primaryKey] = $pdo->lastInsertId();
            $this->exists = true;
        }

        return $result;
    }

    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $pdo = Database::connection();

        $sql = "DELETE FROM " . static::$table .
               " WHERE {$this->primaryKey} = ?";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $this->attributes[$this->primaryKey]
        ]);
    }

    /* =====================================================
     |  Acceso a atributos
     ===================================================== */

    public function __get($key)
    {
        if(array_key_exists($key, $this->attributes)){
            return $this->attributes[$key];
        }

        if(array_key_exists($key, $this->relations)){
            return $this->relations[$key];
        }

        return null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    protected function modelBaseName(): string
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    public static function getTableName(): string
    {
        return static::$table;
    }

    // Relaciones

    public function hasMany(
        string $related,
        string $foreignKey = null,
        string $localKey = 'id'
    ){

        $foreignKey ??= $this->modelBaseName().'_id';

        return $related::where($foreignKey, '=', $this->$localKey);
    }

    public function belongsTo(
        string $related,
        string $foreignKey = null,
        string $ownerKey = 'id'
    ){
        $foreignKey ??= strtolower((new \ReflectionClass($related))->getShortName()).'_id';

        $foreignValue = $this->$foreignKey;

        if ($foreignValue === null) {
            return null;
        }

        return $related::where($ownerKey, '=', $foreignValue)?->first();
    }

    // Eager loading (carga ansiosa)
    public function load(string $relation)
    {
        if(!method_exists($this, $relation)){
            return $this;
        }

        if(array_key_exists($relation, $this->relations)){
            return $this;
        }

        $builder = $this->$relation();

        $result = $builder instanceof Builder
            ? $builder->get()
            : $builder;

        if(is_array($result) && count($result) === 1){
            $result = $result[0];
        }

        $this->relations[$relation] = $result;

        return $this;
    }

    public function loadMany(array $relations)
    {
        foreach($relations as $relation){
            $this->load($relation);
        }

        return $this;
    }

    //obtener llaves foraneas
    public static function foreignKeys(): array
    {
        $table = static::getTableName();

        $sql = "
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$table]);

        $fks = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $fks[$row['COLUMN_NAME']] = [
                'table' => $row['REFERENCED_TABLE_NAME'],
                'column' => $row['REFERENCED_COLUMN_NAME'],
            ];
        }

        return $fks;
    }

    public function getLastSql(): ?string
    {
        return $this->lastSql;
    }
}
