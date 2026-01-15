<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::input.wrapper
        :disabled="$isDisabled()"
        :valid="! $errors->has($getStatePath())"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['fi-fo-tax-rate-selector'])
        "
    >
        <x-filament::input.select
            :disabled="$isDisabled()"
            :id="$getId()"
            :multiple="$isMultiple()"
            :placeholder="$getPlaceholder()"
            :required="$isRequired()"
            :wire:key="$this->getId() . '.forms.' . $getStatePath() . '.select'"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                    ->merge([
                        'wire:loading.attr' => 'disabled',
                        $applyStateBindingModifiers('wire:model') => $getStatePath(),
                    ], escape: false)
            "
        >
            @if (! $isMultiple())
                @if ($placeholder = $getPlaceholder())
                    <option value="">{{ $placeholder }}</option>
                @endif
            @endif

            @foreach ($getOptions() as $value => $label)
                <option
                    @selected($isSelected($value))
                    value="{{ $value }}"
                >
                    {{ $label }}
                </option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</x-dynamic-component>
