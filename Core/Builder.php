<?php

class Builder
{

    protected $table;
    protected $modelClass;
    protected $wheres = [];
    protected $bindings = [];
    protected array $orderBys = [];
    protected array $with = [];

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

    public function get(): array
    {
        $sql = $this->toSql();

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($this->bindings);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = array_map(
            fn($row) => new $this->modelClass($row),
            $rows
        );

        if(!empty($this->with)){
            $this->eagerLoadRelations($models, $this->with);
        }
        return $models;
    }

    public function first()
    {
        return $this->get()[0] ?? null;
    }

    public function orderBy(string $column, string $direction = 'ASC'):self
    {
        $direction = strtoupper($direction);

        if(!in_array($direction, ['ASC', 'DESC'], true)){
            throw new InvalidArgumentException("Dirección inválida para orderBy: {$direction}");
        }

        $this->orderBys[] = [
            'column' => $column,
            'direction' => $direction
        ];

        return $this;
    }

    public function with(array $relations): self
    {
        $this->with = $relations;
        return $this;
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

    protected function eagerLoadRelations(array $models): void
    {
        if(empty($models)){
            return;
        }

        $tree = $this->parseRelations($this->with);

        foreach($tree as $relation => $nested){
            $this->eagerLoadrelation($models, $relation, $nested);
        }
    }

    protected function eagerLoadRelation(array $models, string $relation, array $nested): void
    {
        $first = $models[0];

        if(!method_exists($first, $relation)){
            return ;
        }

        $relationObj = $first->$relation();

        if(!($relationObj instanceof Relation)){
            return ;
        }

        $builder = $relationObj->getBuilder();

        $relatedModels = $builder->get();

        if(empty($relatedModels)){
            foreach($models as $model){
                $model->relations[$relation] =
                    $relationObj->getType() === 'belongsTo' ? null : [];
            }
            return;
        }
        
        $map = [];

        foreach($relatedModels as $related){
            $key = match($relationObj->getType()){
                'hasMany' => $related->{$relationObj->getForeignKey()},
                'belongsTo' => $related->{$relationObj->getLocalKey()},
                default => null
            };

            $map[$key][] = $related;
        }

        foreach($models as $model){
            $localKeyValue = $model->{$relationObj->getLocalKey()};

            if($relationObj->getType() === 'belongsTo'){
                $model->relations[$relation] = $map[$model->{$relationObj->getForeignKey()}][0] ?? null;
            }else{
                $model->relations[$relation] = $map[$localKeyValue] ?? [];
            }
        }

        if(!empty($nested)){
            foreach($models as $model){
                $children = $model->relations[$relation];
                if($relationObj->getType() === 'belongsTo'){
                    $children = $children ? [$children] : [];
                }

                foreach($children as $child){
                    $this->eagerLoadRelations([$child]);
                }
            }
        }
    }

    protected function toSql(): string
    {
        $sql = "SELECT * FROM {$this->table}";

        if($this->wheres){
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if($this->orderBys){
            $orders = array_map(
                fn($o) => "{$o['column']} {$o['direction']}",
                $this->orderBys
            );
            $sql .= " ORDER BY " . implode(', ', $orders);
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