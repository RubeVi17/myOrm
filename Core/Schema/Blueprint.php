<?php

require_once 'ColumnDefinition.php';

class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $foreignKeys = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function id()
    {
        $this->columns[] = "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
    }

    public function string($name, $length = 255)
    {
        $column = new ColumnDefinition($name, "VARCHAR({$length})");
        $this->columns[] = $column;
        return $column;
    }

    public function text($name)
    {
        $column = new ColumnDefinition($name, "TEXT");
        $this->columns[] = $column;
        return $column;
    }

    public function integer($name)
    {
        $column = new ColumnDefinition($name, "INT");
        $this->columns[] = $column;
        return $column;
    }

    public function foreignId($name)
    {
        $column = new ColumnDefinition($name, "INT");
        $column->unsigned();
        $this->columns[] = $column;
        $this->foreignKeys[] = $column;
        return $column;
    }

    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    public function toSql()
    {
        $definitions = [];

        foreach ($this->columns as $column) {
            if (is_string($column)) {
                $definitions[] = $column;
            } else {
                $definitions[] = $column->toSql();
            }
        }

        // Foreign keys
        foreach ($this->foreignKeys as $column) {
            if (!empty($column->foreign)) {
                $definitions[] =
                    "FOREIGN KEY ({$column->name})
                     REFERENCES {$column->foreign['on']}({$column->foreign['references']})";
            }
        }

        return "CREATE TABLE {$this->table} (" . implode(", ", $definitions) . ")";
    }
}
