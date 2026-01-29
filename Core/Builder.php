<?php

class Builder
{

    protected $table;
    protected $modelClass;
    protected $wheres = [];
    protected $bindings = [];

    public function __construct(string $table, string $modelClass)
    {
        $this->table = $table;
        $this->modelClass = $modelClass;
    }

    public function where(string $column, string $operator, $value = null)
    {
        if($value === null){
            $value = $operator;
            $operator = '=';
        }


        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;

        return $this;

    }


    public function get()
    {
        $sql = $this->toSql();

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($this->bindings);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn($row) => new $this->modelClass($row),
            $rows
        );
    }

    public function first()
    {
        return $this->get()[0] ?? null;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) as aggregate FROM {$this->table}";

        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($this->bindings);

        return (int) $stmt->fetchColumn();
    }
    
    protected function toSql(): string
    {
        $sql = "SELECT * FROM {$this->table}";

        if($this->wheres){
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        return $sql;
    }

    public function sqlDebug(): string
    {
        $sql = $this->toSql();
        foreach ($this->bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'{$binding}'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }


}