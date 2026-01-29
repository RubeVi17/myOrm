<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Builder.php';
require_once __DIR__ . '/Relations/Relation.php';

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

    protected array $original = [];

    protected bool $softDeletes = false;

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

    public static function __callStatic($method, $arguments)
    {
        $builder = new Builder(static::$table, static::class);

        if(method_exists($builder, $method)){
            return $builder->$method(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
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

    public function update(array $attributes): bool
    {
        if(!empty($this->fillable)){
            $attributes = array_intersect_key(
                $attributes,
                array_flip($this->fillable)
            );
        }

        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

        if($this->timestamps) {
            $this->attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->save();
    }

    public function save(): bool
    {
        $pdo = Database::connection();

        // UPDATE
        if ($this->exists) {

            if ($this->timestamps) {
                $this->attributes['updated_at'] = date('Y-m-d H:i:s');
            }

            $sets = [];
            $values = [];

            foreach ($this->attributes as $key => $value) {
                if ($key === $this->primaryKey) continue;
                if (isset($this->original[$key]) && $this->original[$key] === $value) continue;

                $sets[] = "{$key} = ?";
                $values[] = $value;
            }

            if (empty($sets)) {
                return true;
            }

            $values[] = $this->attributes[$this->primaryKey];

            $sql = "UPDATE " . static::$table .
                " SET " . implode(', ', $sets) .
                " WHERE {$this->primaryKey} = ?";

            $stmt = $pdo->prepare($sql);
            $this->lastSql = $sql;

            $result = $stmt->execute($values);

            if ($result) {
                $this->original = $this->attributes;
            }

            return $result;
        }

        // INSERT
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $this->attributes['created_at'] ??= $now;
            $this->attributes['updated_at'] ??= $now;
        }

        $columns = array_keys($this->attributes);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO " . static::$table .
            " (" . implode(', ', $columns) . ")" .
            " VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $pdo->prepare($sql);
        $this->lastSql = $sql;

        $result = $stmt->execute(array_values($this->attributes));

        if ($result) {
            $this->attributes[$this->primaryKey] = $pdo->lastInsertId();
            $this->exists = true;
            $this->original = $this->attributes;
        }

        return $result;
    }


    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        if($this->softDeletes){
            $this->attributes['deleted_at'] = date('Y-m-d H:i:s');
            return $this->save();
        }

        $pdo = Database::connection();

        $sql = "DELETE FROM " . static::$table .
               " WHERE {$this->primaryKey} = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $this->attributes[$this->primaryKey]
        ]);
        if ($result) {
            $this->exists = false;
            $this->attributes = [];
        }
        return $result;
    }

    /* =====================================================
     |  Acceso a atributos
     ===================================================== */

    public function __get($key)
    {
        if (isset($this->relations[$key])) {

            if ($this->relations[$key] instanceof Relation) {
                throw new RuntimeException(
                    "Relation '{$key}' was not resolved. Did you forget to call get() or eager load it?"
                );
            }

            return $this->relations[$key];
        }

        return $this->attributes[$key] ?? null;
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

        $builder = $related::where($foreignKey, '=', $this->$localKey);

        return new Relation(
            $builder,
            'hasMany',
            $foreignKey,
            $localKey
        );
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

        $builder = $related::where($ownerKey, '=', $foreignValue);

        return new Relation(
            $builder,
            'belongsTo',
            $foreignKey,
            $ownerKey
        );
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

        $relationObj = $this->$relation();

        if($relationObj instanceof Relation){
            $data = $relationObj->getType() === 'belongsTo'
                ? $relationObj->first()
                : $relationObj->get();

            
            $this->relations[$relation] = $data;
        }
        

        return $this;
    }

    public function loadMany(array $relations): self
    {
        $tree = $this->parseRelations($relations);

        foreach($tree as $relation => $nested){
            $this->loadRelationRecursive($relation, $nested);
        }

        return $this;
    }

    protected function parseRelations(array $relations): array
    {
        $tree = [];

        foreach($relations as $relation){
            $parts = explode('.', $relation);
            $current = &$tree;

            foreach($parts as $part){
                if(!isset($current[$part])){
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }
        return $tree;
    }

    protected function loadRelationRecursive(string $relation, array $nested): void
    {
        if (!method_exists($this, $relation)) {
            return;
        }

        $relationObj = $this->$relation();

        if ($relationObj instanceof Relation) {

            $data = $relationObj->getType() === 'belongsTo'
                ? $relationObj->first()
                : $relationObj->get();

            $this->relations[$relation] = $data;
        } else {
            $this->relations[$relation] = $relationObj;
            $data = $relationObj;
        }

        if (empty($nested)) {
            return;
        }

        if (is_array($data)) {
            foreach ($data as $model) {
                if ($model instanceof Model) {
                    foreach ($nested as $nestedRelation => $nestedNested) {
                        $model->loadRelationRecursive($nestedRelation, $nestedNested);
                    }
                }
            }
        } elseif ($data instanceof Model) {
            foreach ($nested as $nestedRelation => $nestedNested) {
                $data->loadRelationRecursive($nestedRelation, $nestedNested);
            }
        }
    }

    public function orderBy(string $column, string $direction = 'ASC'): Builder
    {
        return static::query()->orderBy($column, $direction);
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
