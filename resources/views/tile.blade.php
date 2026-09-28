<x-dashboard-tile :position="$position" :refresh-interval="$refreshIntervalInSeconds">
    <style>
        .netdata-honeycomb {
            display: grid;
            grid-auto-flow: row;
            justify-content: center;
            align-content: center;
            grid-template-columns: repeat(var(--half-columns, 2), calc((var(--hexagon-size, 0px) + var(--hexagon-gap)) / 2));
            grid-auto-rows: calc(var(--hexagon-size, 0px) * 0.866 + var(--hexagon-gap));
            padding-bottom: calc(var(--hexagon-size, 0px) * 0.2887 - var(--hexagon-gap));
            --hexagon-gap: 4px;
            visibility: hidden;
        }

        .netdata-hexagon {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            justify-self: center;
            width: var(--hexagon-size);
            height: calc(var(--hexagon-size) * 1.1547);
            grid-column-end: span 2;
            clip-path: polygon(0% 25%, 0% 75%, 50% 100%, 100% 75%, 100% 25%, 50% 0%);
            font-size: clamp(8px, calc(var(--hexagon-size) * 0.14), 16px);
            overflow: hidden;
        }
    </style>

    <div class="h-full flex flex-col">
        @if($nodes->isNotEmpty())
            <div class="flex justify-end mb-2 text-dimmed text-sm tabular-nums">
                {{ $onlineCount }}/{{ $nodes->count() }} online
            </div>
        @endif

        @if($nodes->isEmpty())
            <div class="flex grow items-center justify-center text-dimmed text-sm">
                No data yet
            </div>
        @else
            <div
                class="grow min-h-0 overflow-hidden"
                x-data="{
                    maxHexagonSize: 112,
                    gap: 4,

                    init() {
                        new ResizeObserver(() => this.layout()).observe(this.$el);
                        new MutationObserver(() => this.layout()).observe(this.$refs.honeycomb, { childList: true });
                        this.layout();
                    },

                    layout() {
                        const honeycomb = this.$refs.honeycomb;
                        const count = honeycomb.querySelectorAll('.netdata-hexagon').length;
                        const width = this.$el.clientWidth;
                        const height = this.$el.clientHeight;

                        if (count === 0 || width === 0 || height === 0) {
                            return;
                        }

                        let best = { size: 0, columns: 1, rows: count };

                        for (let columns = 1; columns <= count; columns++) {
                            const rows = Math.ceil(count / columns);
                            const rowOffset = rows > 1 ? 0.5 : 0;
                            const sizeByWidth = width / (columns + rowOffset) - this.gap;
                            const sizeByHeight = (height - (rows - 1) * this.gap) / (1.1547 * (0.75 * rows + 0.25));
                            const size = Math.min(sizeByWidth, sizeByHeight, this.maxHexagonSize);

                            if (size > best.size) {
                                best = { size, columns, rows };
                            }
                        }

                        const halfColumns = best.columns * 2 + (best.rows > 1 ? 1 : 0);
                        const pairLength = best.columns * 2;
                        const hexagon = `#${honeycomb.id} > .netdata-hexagon`;
                        let rules = '';

                        for (let column = 0; column < best.columns; column++) {
                            rules += `${hexagon}:nth-child(${pairLength}n + ${column + 1}) { grid-column-start: ${column * 2 + 1}; }`;
                            rules += `${hexagon}:nth-child(${pairLength}n + ${best.columns + column + 1}) { grid-column-start: ${column * 2 + 2}; }`;
                        }

                        rules += `#${honeycomb.id} { --hexagon-size: ${Math.floor(best.size)}px; --hexagon-gap: ${this.gap}px; --half-columns: ${halfColumns}; visibility: visible; }`;

                        this.$refs.layout.textContent = rules;
                    },
                }"
            >
                <style x-ref="layout" wire:ignore></style>
                {{-- Layout state lives in the wire:ignore style tag: Livewire's morph strips attributes set by JS from the honeycomb on every poll --}}
                <div class="netdata-honeycomb" id="netdata-honeycomb-{{ $this->getId() }}" x-ref="honeycomb">
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
            </div>
        @endif
    </div>
</x-dashboard-tile>
