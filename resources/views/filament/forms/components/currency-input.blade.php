<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::input.wrapper
        :disabled="$isDisabled()"
        :valid="! $errors->has($getStatePath())"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['fi-fo-currency-input'])
        "
    >
        <x-filament::input
            :disabled="$isDisabled()"
            :id="$getId()"
            :inlinePrefix="$isPrefixInline()"
            :inlineSuffix="$isSuffixInline()"
            :placeholder="$getPlaceholder()"
            :prefix="$getPrefix()"
            :prefix-icon="$getPrefixIcon()"
            :prefix-icon-color="$getPrefixIconColor()"
            :required="$isRequired()"
            :suffix="$getSuffix()"
            :suffix-icon="$getSuffixIcon()"
            :suffix-icon-color="$getSuffixIconColor()"
            :type="$getType()"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                    ->merge($getExtraInputAttributes(), escape: false)
                    ->merge([
                        'autocomplete' => $getAutocomplete(),
                        'autofocus' => $isAutofocused(),
                        'inputmode' => $getInputMode(),
                        'max' => (! $isConcealed()) ? $getMaxValue() : null,
                        'maxlength' => (! $isConcealed()) ? $getMaxLength() : null,
                        'min' => (! $isConcealed()) ? $getMinValue() : null,
                        'minlength' => (! $isConcealed()) ? $getMinLength() : null,
                        'pattern' => $getPattern(),
                        'readonly' => $isReadOnly(),
                        'step' => $getStep(),
                        $applyStateBindingModifiers('wire:model') => $getStatePath(),
                    ], escape: false)
            "
        />
    </x-filament::input.wrapper>
</x-dynamic-component>
