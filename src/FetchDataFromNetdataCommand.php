<?php

namespace Creacoon\NetdataTile;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FetchDataFromNetdataCommand extends Command
{
    protected $signature = 'dashboard:fetch-data-from-netdata-api';

    protected $description = 'Fetch the status of every node streaming to the Netdata parent';

    public function handle(): int
    {
        $parentUrl = config('dashboard.tiles.netdata.parent_url');

        if (blank($parentUrl)) {
            $this->error('Set `dashboard.tiles.netdata.parent_url` to the URL of your Netdata parent.');

            return self::FAILURE;
        }

        $this->info("Fetching nodes from `{$parentUrl}`...");

        try {
            $response = $this->fetchNodes($parentUrl);
        } catch (ConnectionException $exception) {
            $this->error("Could not connect to the Netdata parent: {$exception->getMessage()}");

            return self::FAILURE;
        }

        if ($response->failed()) {
            $this->error("Failed to fetch nodes. Status: {$response->status()}");

            return self::FAILURE;
        }

        $nodes = $response->collect('nodes')
            ->map(fn (array $node): array => [
                'name' => $node['nm'],
                'status' => NodeStatus::fromNode($node)->value,
                'critical' => $node['health']['alerts']['critical'] ?? 0,
                'warning' => $node['health']['alerts']['warning'] ?? 0,
            ])
            ->sortBy('name', SORT_NATURAL)
            ->values()
            ->all();

        NetdataStore::make()->setNodes($nodes);

        $this->comment('Stored '.count($nodes).' nodes.');

        return self::SUCCESS;
    }

    private function fetchNodes(string $parentUrl): Response
    {
        return Http::baseUrl($parentUrl)
            ->acceptJson()
            ->timeout(10)
            ->get('/api/v2/nodes');
    }
}
