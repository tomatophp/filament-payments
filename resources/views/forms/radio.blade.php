@php
    use Filament\Support\Enums\GridDirection;

    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $statePath = $getStatePath();
    $hasError = $errors->has($statePath);

    $containerAttributes = $getExtraAttributeBag();

    if (! $isInline) {
        $containerAttributes = $containerAttributes->grid($getColumns(), $gridDirection);
    }

    $gatewayModels = \TomatoPHP\FilamentPayments\Models\PaymentGateway::query()
        ->whereIn('id', array_keys($getOptions()))
        ->get()
        ->keyBy('id');
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        {{ $containerAttributes
            ->merge(['role' => 'radiogroup'], escape: false)
            ->class(['fi-fo-radio gap-4', 'fi-inline' => $isInline]) }}
    >
        @foreach ($getOptions() as $value => $label)
            @php($gateway = $gatewayModels->get($value))
            <label class="fi-fo-radio-label flex w-full gap-x-3">
                <input
                    type="radio"
                    {{ $getExtraInputAttributeBag()
                        ->merge([
                            'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                            'id' => $id . '-' . $value,
                            'name' => $id,
                            'value' => $value,
                            'wire:loading.attr' => 'disabled',
                            $applyStateBindingModifiers('wire:model') => $statePath,
                        ], escape: false)
                        ->class([
                            'fi-radio-input mt-1',
                            'fi-valid' => ! $hasError,
                            'fi-invalid' => $hasError,
                        ]) }}
                />

                <div class="grid w-full text-sm leading-6">
                    <div class="flex justify-between">
                        <div class="w-full">
                            <span class="font-medium text-gray-950 dark:text-white">
                                {{ $label }}
                            </span>

                            @if ($hasDescription($value))
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>

                        @if ($gateway?->getFirstMediaUrl('image'))
                            <div>
                                <img
                                    src="{{ $gateway->getFirstMediaUrl('image') }}"
                                    alt="{{ $gateway->name }}"
                                    class="w-32"
                                />
                            </div>
                        @endif
                    </div>
                </div>
            </label>
        @endforeach
    </div>
</x-dynamic-component>
