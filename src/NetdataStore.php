<?php

namespace Creacoon\NetdataTile;

use Spatie\Dashboard\Models\Tile;

class NetdataStore
{
    private Tile $tile;

    public static function make(): static
    {
        return new static;
    }

    final public function __construct()
    {
        $this->tile = Tile::firstOrCreateForName('netdata');
    }

    /** @param list<array{name: string, status: string, critical: int, warning: int}> $nodes */
    public function setNodes(array $nodes): self
    {
        $this->tile->putData('nodes', $nodes);

        return $this;
    }

    /** @return list<array{name: string, status: string, critical: int, warning: int}> */
    public function nodes(): array
    {
        return $this->tile->getData('nodes') ?? [];
    }
}
