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

    /* =====================================================
     |  Query Builder
     ===================================================== */

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

    public static function create(array $attributes): static
    {
        $model = new static($attributes);
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
}
