<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-invoice-builder'])
        }}
    >
        @if (count($containers = $getChildComponentContainers()))
            <ul>
                <x-filament::grid
                    :default="$getGridColumns('default')"
                    :sm="$getGridColumns('sm')"
                    :md="$getGridColumns('md')"
                    :lg="$getGridColumns('lg')"
                    :xl="$getGridColumns('xl')"
                    :'2xl'="$getGridColumns('2xl')"
                    :wire:end.stop="'mountFormComponentAction(\'' . $getStatePath() . '\', \'reorder\', { items: $event.target.sortable.toArray() })'"
                    :x-sortable="$isReorderable() && (! $isDisabled()) && (count($containers) > 1)"
                    class="gap-4"
                >
                    @foreach ($containers as $uuid => $item)
                        <x-filament::grid.column
                            :wire:key="$this->getId() . '.items.' . $uuid"
                            :x-sortable-item="$isReorderable() ? $uuid : null"
                            class="fi-fo-repeater-item"
                        >
                            {{ $item }}
                        </x-filament::grid.column>
                    @endforeach
                </x-filament::grid>
            </ul>
        @endif

        @if (! $isDisabled())
            @if ($isAddable())
                <x-filament::button
                    :wire:click="'mountFormComponentAction(\'' . $getStatePath() . '\', \'add\')'"
                    size="sm"
                    type="button"
                >
                    {{ $getAddActionLabel() }}
                </x-filament::button>
            @endif
        @endif
    </div>
</x-dynamic-component>
