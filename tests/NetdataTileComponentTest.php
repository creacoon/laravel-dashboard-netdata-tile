<?php

use Creacoon\NetdataTile\NetdataStore;
use Creacoon\NetdataTile\NetdataTileComponent;
use Livewire\Livewire;

it('renders a hexagon in the status colour for every node', function () {
    NetdataStore::make()->setNodes([
        ['name' => 'old-node', 'status' => 'offline', 'critical' => 0, 'warning' => 0],
        ['name' => 'parent', 'status' => 'warning', 'critical' => 0, 'warning' => 2],
        ['name' => 'web10', 'status' => 'critical', 'critical' => 1, 'warning' => 1],
        ['name' => 'web2', 'status' => 'healthy', 'critical' => 0, 'warning' => 0],
    ]);

    $component = Livewire::test(NetdataTileComponent::class, ['position' => 'a1:b2']);

    $component->assertSeeHtmlInOrder([
        'bg-gray-500 opacity-60 text-white" data-status="offline"', 'old-node',
        'bg-orange-500 text-white" data-status="warning"', 'parent', '2W',
        'bg-red-600 text-white" data-status="critical"', 'web10', '1C', '1W',
        'bg-green-600 text-white" data-status="healthy"', 'web2',
    ]);
});

it('counts every node that is not offline as online', function () {
    NetdataStore::make()->setNodes([
        ['name' => 'old-node', 'status' => 'offline', 'critical' => 0, 'warning' => 0],
        ['name' => 'web10', 'status' => 'critical', 'critical' => 1, 'warning' => 0],
        ['name' => 'web2', 'status' => 'healthy', 'critical' => 0, 'warning' => 0],
    ]);

    $component = Livewire::test(NetdataTileComponent::class, ['position' => 'a1:b2']);

    $component->assertSee('2/3 online');
});

it('shows a placeholder before any nodes have been fetched', function () {
    $component = Livewire::test(NetdataTileComponent::class, ['position' => 'a1:b2']);

    $component->assertSee('No data yet')
        ->assertDontSee('online');
});

it('escapes node names', function () {
    NetdataStore::make()->setNodes([
        ['name' => '<script>alert("node")</script>', 'status' => 'healthy', 'critical' => 0, 'warning' => 0],
    ]);

    $component = Livewire::test(NetdataTileComponent::class, ['position' => 'a1:b2']);

    $component->assertSeeHtml('&lt;script&gt;')
        ->assertDontSeeHtml('<script>alert("node")</script>');
});
