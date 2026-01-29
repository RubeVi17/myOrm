<?php

require_once 'ColumnDefinition.php';

class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $foreignKeys = [];
    protected string $mode;
    protected array $addColumns = [];
    protected array $dropColumns = [];

    public function __construct(string $table, string $mode = 'create')
    {
        $this->table = $table;
        $this->mode = $mode;
    }

    public function id()
    {
        $this->columns[] = "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
    }

    public function boolean($name)
    {
        $column = new ColumnDefinition($name, "BOOLEAN");
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        return $column;
    }

    public function string($name, $length = 255)
    {
        $column = new ColumnDefinition($name, "VARCHAR({$length})");
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        return $column;
    }

    public function date($name)
    {
        $column = new ColumnDefinition($name, "DATE");
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        return $column;
    }

    public function dateTime($name)
    {
        $column = new ColumnDefinition($name, "DATETIME");
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        return $column;
    }

    public function text($name)
    {
        $column = new ColumnDefinition($name, "TEXT");
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        return $column;
    }

    public function integer($name)
    {
        $column = new ColumnDefinition($name, "INT");
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        return $column;
    }

    public function foreignId($name)
    {
        $column = new ColumnDefinition($name, "INT");
        $column->unsigned();
        if ($this->mode === 'create') {
            $this->columns[] = $column;
        } else {
            $this->addColumns[] = $column;
        }
        $this->foreignKeys[] = $column;
        return $column;
    }

    public function timestamps()
    {
        if ($this->mode === 'create') {
            $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        } else {
            $this->addColumns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
            $this->addColumns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        }
    }

    public function dropColumn($name)
    {
        $this->dropColumns[] = $name;
    }

    public function toSql()
    {
        if($this->mode === 'create'){
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

        // ALTER MODE
        $statements = [];

        foreach ($this->addColumns as $column) {
            $sql = "ALTER TABLE {$this->table} ADD " . $column->toSql();
            if($column->getAfter()){
                $sql .= " AFTER {$column->getAfter()}";
            }

            $statements[] = $sql;
        }

        foreach ($this->dropColumns as $column) {
            $statements[] =
                "ALTER TABLE {$this->table} DROP COLUMN {$column}";
        }

        foreach ($this->foreignKeys as $column) {
            if (!empty($column->foreign)) {
                $statements[] =
                    "ALTER TABLE {$this->table}
                    ADD FOREIGN KEY ({$column->name})
                    REFERENCES {$column->foreign['on']}({$column->foreign['references']})";
            }
        }

        return $statements;
    }
    
}
