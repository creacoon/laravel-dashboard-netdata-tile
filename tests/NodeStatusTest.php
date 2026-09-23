<?php

use Creacoon\NetdataTile\NodeStatus;

it('derives :dataset', function (array $node, NodeStatus $expectedStatus) {
    expect(NodeStatus::fromNode($node))->toBe($expectedStatus);
})->with([
    'healthy for a reachable node without alerts' => [
        ['state' => 'reachable', 'health' => ['alerts' => ['critical' => 0, 'warning' => 0]]],
        NodeStatus::Healthy,
    ],
    'warning for a reachable node with only warnings' => [
        ['state' => 'reachable', 'health' => ['alerts' => ['critical' => 0, 'warning' => 3]]],
        NodeStatus::Warning,
    ],
    'critical over warning for a reachable node with both' => [
        ['state' => 'reachable', 'health' => ['alerts' => ['critical' => 1, 'warning' => 3]]],
        NodeStatus::Critical,
    ],
    'offline for a stale node even when it has alerts' => [
        ['state' => 'stale', 'health' => ['alerts' => ['critical' => 2, 'warning' => 0]]],
        NodeStatus::Offline,
    ],
    'offline for a node without a state' => [
        ['health' => ['alerts' => ['critical' => 0, 'warning' => 0]]],
        NodeStatus::Offline,
    ],
    'healthy for a reachable node without health data' => [
        ['state' => 'reachable'],
        NodeStatus::Healthy,
    ],
]);
