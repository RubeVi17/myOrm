<?php
class Relation
{
    public function __construct(
        protected Builder $builder,
        protected string $type,
        protected string $foreignKey,
        protected string $localKey
    ) {}

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    public function getType(): string
    {
        return $this->type;
    }

    // Passthrough
    public function get(): array
    {
        return $this->builder->get();
    }

    public function first(): ?Model
    {
        return $this->builder->first();
    }
}
