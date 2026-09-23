# Netdata tile for Laravel Dashboard

A tile for [spatie/laravel-dashboard](https://github.com/spatie/laravel-dashboard) that shows every node streaming to a Netdata parent as a hexagon, coloured by its status:

| Colour | Status |
|---|---|
| Green | Reachable, no raised alerts |
| Orange | Reachable, at least one warning |
| Red | Reachable, at least one critical alert |
| Grey | Stale or unreachable |

Each hexagon shows the node name and its critical (`C`) and warning (`W`) alert counts. The header shows how many nodes are online.

The data comes from the parent's `/api/v2/nodes` endpoint, so the application must be able to reach the parent on its API port (19999 by default).

## Installation

```bash
composer require creacoon/laravel-dashboard-netdata-tile
```

Add the tile configuration to the `tiles` key of `config/dashboard.php`:

```php
'netdata' => [
    'parent_url' => env('NETDATA_PARENT_URL'),
    'refresh_interval_in_seconds' => 60,
],
```

Schedule the fetch command in your console kernel or `routes/console.php`:

```php
$schedule->command(\Creacoon\NetdataTile\FetchDataFromNetdataCommand::class)->everyMinute();
```

Add the tile to your dashboard:

```blade
<x-dashboard>
    <livewire:netdata-tile position="a1:b2" />
</x-dashboard>
```

When the parent is unreachable or returns an error, the command fails and the tile keeps showing the last fetched nodes.

## Customising the view

```bash
php artisan vendor:publish --tag=dashboard-netdata-tile-views
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
