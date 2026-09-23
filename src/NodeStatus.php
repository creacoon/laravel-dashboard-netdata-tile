<?php

namespace Creacoon\NetdataTile;

enum NodeStatus: string
{
    case Healthy = 'healthy';
    case Warning = 'warning';
    case Critical = 'critical';
    case Offline = 'offline';

    /** @param array<string, mixed> $node A node as returned by the Netdata `/api/v2/nodes` endpoint */
    public static function fromNode(array $node): self
    {
        if (($node['state'] ?? null) !== 'reachable') {
            return self::Offline;
        }

        if (($node['health']['alerts']['critical'] ?? 0) > 0) {
            return self::Critical;
        }

        if (($node['health']['alerts']['warning'] ?? 0) > 0) {
            return self::Warning;
        }

        return self::Healthy;
    }

    public function backgroundClass(): string
    {
        return match ($this) {
            self::Healthy => 'bg-green-600',
            self::Warning => 'bg-orange-500',
            self::Critical => 'bg-red-600',
            self::Offline => 'bg-gray-500 opacity-60',
        };
    }
}
