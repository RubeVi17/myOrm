<?php

class ColumnDefinition
{

    public string $name;
    public string $type;
    public array $modifiers = [];
    public array $foreign = [];
    protected ?string $after = null;
    public ?string $onDelete = null;
    public ?string $onUpdate = null;
    

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function getAfter(): ?string
    {
        return $this->after;
    }

    public function after(string $column)
    {
        $this->after = $column;
        return $this;
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
        if(is_bool($value)){
            $value = $value ? 'TRUE' : 'FALSE';
        } elseif (is_null($value)) {
            $value = 'NULL';
        } elseif (is_numeric($value)) {
            // leave as is
        } else {
            $value = "'{$value}'";
        }

        $this->modifiers[] = "DEFAULT {$value}";
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
        $this->foreign = [
            'on' => $table,
            'references' => $column,
        ];

        return $this;
    }

    public function onDelete(string $action): self
    {
        $this->onDelete = strtoupper($action);
        return $this;
    }

    public function onUpdate(string $action): self
    {
        $this->onUpdate = strtoupper($action);
        return $this;
    }

    public function toSql(): string
    {
        $sql = "{$this->name} {$this->type}";

        // 1) Type modifiers (van inmediatamente después del tipo)
        $typeMods = [];
        $otherMods = [];

        foreach ($this->modifiers as $m) {
            $u = strtoupper(trim($m));
            if ($u === 'UNSIGNED' || $u === 'ZEROFILL') {
                $typeMods[] = $u;
            } else {
                $otherMods[] = $m;
            }
        }

        if ($typeMods) {
            $sql .= " " . implode(" ", $typeMods);
        }

        // 2) Nullability
        if (!in_array('NULL', $this->modifiers, true)) {
            $sql .= " NOT NULL";
        }

        // 3) Resto de modifiers (DEFAULT, UNIQUE, etc.)
        if ($otherMods) {
            $sql .= " " . implode(" ", $otherMods);
        }

        return $sql;
    }



}