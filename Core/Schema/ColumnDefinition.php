<?php

class ColumnDefinition
{

    public string $name;
    public string $type;
    public array $modifiers = [];
    public array $foreign = [];
    

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }


    public function nullable()
    {
        $this->modifiers[] = "NULL";
        return $this;
    }

    public function unique()
    {
        $this->modifiers[] = "UNIQUE";
        return $this;
    }

    public function default($value)
    {
        $this->modifiers[] = "DEFAULT '{$value}'";
        return $this;
    }

    public function unsigned()
    {
        $this->modifiers[] = "UNSIGNED";
        return $this;
    }

    // Llaves foraneas

    public function references(string $column)
    {
        $this->foreign['references'] = $column;
        return $this;
    }

    public function on(string $table)
    {
        $this->foreign['on'] = $table;
        return $this;
    }

    public function constrained(string $table, $column = 'id')
    {
        return $this->references($column)->on($table);
    }

    public function toSql(): string
    {
        $sql = "{$this->name} {$this->type}";

        if (!in_array('NULL', $this->modifiers)) {
            $sql .= " NOT NULL";
        }

        if ($this->modifiers) {
            $sql .= " " . implode(" ", $this->modifiers);
        }

        return $sql;
    }



}