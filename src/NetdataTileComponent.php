<?php

namespace Creacoon\NetdataTile;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NetdataTileComponent extends Component
{
    public string $position;

    public function mount(string $position): void
    {
        $this->position = $position;
    }

    public function render(): View
    {
        $nodes = collect(NetdataStore::make()->nodes());

        return view('dashboard-netdata-tile::tile', [
            'nodes' => $nodes,
            'onlineCount' => $nodes->where('status', '!=', NodeStatus::Offline->value)->count(),
            'refreshIntervalInSeconds' => config('dashboard.tiles.netdata.refresh_interval_in_seconds', 60),
        ]);
    }
}
