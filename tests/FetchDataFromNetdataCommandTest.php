<?php

use Creacoon\NetdataTile\NetdataStore;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
});

function storePreviousNodes(): array
{
    $previousNodes = [['name' => 'web2', 'status' => 'healthy', 'critical' => 0, 'warning' => 0]];

    NetdataStore::make()->setNodes($previousNodes);

    return $previousNodes;
}

it('stores every node sorted by name with its status and alert counts', function () {
    Http::fake([
        'http://netdata-parent.test:19999/api/v2/nodes' => Http::response(file_get_contents(__DIR__.'/Fixtures/nodes.json')),
    ]);

    $this->artisan('dashboard:fetch-data-from-netdata-api')->assertSuccessful();

    expect(NetdataStore::make()->nodes())->toBe([
        ['name' => 'old-node', 'status' => 'offline', 'critical' => 0, 'warning' => 0],
        ['name' => 'parent', 'status' => 'warning', 'critical' => 0, 'warning' => 2],
        ['name' => 'web2', 'status' => 'healthy', 'critical' => 0, 'warning' => 0],
        ['name' => 'web10', 'status' => 'critical', 'critical' => 1, 'warning' => 1],
    ]);
});

it('fails and keeps the previous nodes when the parent returns 500', function () {
    $previousNodes = storePreviousNodes();

    Http::fake([
        'http://netdata-parent.test:19999/api/v2/nodes' => Http::response('Internal Server Error', 500),
    ]);

    $this->artisan('dashboard:fetch-data-from-netdata-api')
        ->expectsOutputToContain('Failed to fetch nodes. Status: 500')
        ->assertFailed();

    expect(NetdataStore::make()->nodes())->toBe($previousNodes);
});

it('fails and keeps the previous nodes when the parent is unreachable', function () {
    $previousNodes = storePreviousNodes();

    Http::fake([
        'http://netdata-parent.test:19999/api/v2/nodes' => fn () => throw new ConnectionException('Connection timed out'),
    ]);

    $this->artisan('dashboard:fetch-data-from-netdata-api')
        ->expectsOutputToContain('Could not connect to the Netdata parent: Connection timed out')
        ->assertFailed();

    expect(NetdataStore::make()->nodes())->toBe($previousNodes);
});

it('fails without a request when no parent url is configured', function () {
    config()->set('dashboard.tiles.netdata.parent_url', null);

    $this->artisan('dashboard:fetch-data-from-netdata-api')
        ->expectsOutputToContain('Set `dashboard.tiles.netdata.parent_url`')
        ->assertFailed();

    Http::assertNothingSent();
});
