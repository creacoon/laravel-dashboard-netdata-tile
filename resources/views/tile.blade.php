<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <style>
        .netdata-honeycomb {
            --hexagon-size: 7rem;
            --hexagon-gap: 0.25rem;
            --row-height: calc(var(--hexagon-size) * 1.732 + 4 * var(--hexagon-gap) - 1px);
            font-size: 0;
        }

        .netdata-honeycomb::before {
            content: "";
            float: left;
            width: calc(var(--hexagon-size) / 2 + var(--hexagon-gap));
            height: 120%;
            shape-outside: repeating-linear-gradient(transparent 0 calc(var(--row-height) - 3px), #000 0 var(--row-height));
        }

        .netdata-hexagon {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            vertical-align: top;
            width: var(--hexagon-size);
            height: calc(var(--hexagon-size) * 1.1547);
            margin: var(--hexagon-gap);
            margin-bottom: calc(var(--hexagon-gap) - var(--hexagon-size) * 0.2885);
            clip-path: polygon(0% 25%, 0% 75%, 50% 100%, 100% 75%, 100% 25%, 50% 0%);
            font-size: 1rem;
        }
    </style>

    <div class="h-full flex flex-col">
        <div class="flex items-center justify-between mb-2">
            <div class="font-medium text-dimmed text-sm uppercase tracking-wide">
                Netdata
            </div>
            @if($nodes->isNotEmpty())
                <div class="text-dimmed text-sm tabular-nums">
                    {{ $onlineCount }}/{{ $nodes->count() }} online
                </div>
            @endif
        </div>

        @if($nodes->isEmpty())
            <div class="flex grow items-center justify-center text-dimmed text-sm">
                No data yet
            </div>
        @else
            <div class="netdata-honeycomb grow">
                @foreach($nodes as $node)
                    @php($status = \Creacoon\NetdataTile\NodeStatus::from($node['status']))
                    <div class="netdata-hexagon {{ $status->backgroundClass() }} text-white" data-status="{{ $status->value }}">
                        <span class="font-medium leading-tight px-2 truncate max-w-full">{{ $node['name'] }}</span>
                        @if($node['critical'] > 0 || $node['warning'] > 0)
                            <span class="tabular-nums opacity-90">
                                @if($node['critical'] > 0){{ $node['critical'] }}C @endif
                                @if($node['warning'] > 0){{ $node['warning'] }}W @endif
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-dashboard-tile>
