<div class="h-screen flex flex-col md:flex-row md:justify-between">
    <div class="px-6 py-12 dark:bg-gray-950 dark:text-white shadow-sm h-screen">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <x-filament-panels::logo  />
                {{ env('APP_NAME', 'Laravel') }}
            </h1>
            <h2 class="text-lg font-semibold">
                {{ trans('filament-payments::messages.view.title_pay_page') }}
            </h2>
            @if(auth()->check())
            <div class="flex items-center mt-6 gap-2">
                <div>
                    {{ trans('filament-payments::messages.view.signed_in_as') }}
                </div>
                <div>
                    {{ auth()->user()->name }}.
                </div>
            </div>
            <div class="text-sm">
                {{ trans('filament-payments::messages.view.managing_billing_for') }} {{ auth()->user()->name }}.
            </div>
            @endif
            <div class="mt-6">
                {{ trans('filament-payments::messages.view.contact_us') }}
            </div>
            <div class="flex flex-col mt-6">
                @if(method_exists($payment->model, 'hasMedia') && $payment->model->hasMedia('avatar'))
                    <img src="{{ $payment->model->getFirstMediaUrl('avatar') }}" alt="Logo"  width="100" height="100" class="mb-4 rounded-full" style="aspect-ratio: 100 / 100; object-fit: cover;" />
                @endif
                <h2 class="text-xl font-bold">{{ $payment->model->name }}</h2>
                <h3 class="text-lg font-semibold">{{ $payment->detail }}</h3>
                <div class="w-full border-t border-gray-200 dark:border-gray-700 my-4"></div>
                <div class="flex justify-between w-full mb-2">
                    <span class="text-gray-400">{{ trans('filament-payments::messages.view.amount') }}</span>
                    <span class="text-gray-800">{{ Number::currency($payment->amount, $payment->method_currency) }}</span>
                </div>
                <div class="flex justify-between w-full mb-2">
                    <span class="text-gray-400">{{ trans('filament-payments::messages.view.payment_gateway_fee') }}</span>
                    <span class="text-gray-800">{{ Number::currency($payment->charge, $payment->method_currency) }}</span>
                </div>
                <div class="w-full border-t border-gray-200 dark:border-gray-700 my-4"></div>
                <div class="flex justify-between w-full">
                    <span class="text-lg font-bold">{{ trans('filament-payments::messages.view.total') }}</span>
                    <span class="text-2xl font-bold text-black">
                        {{ Number::currency($payment->final_amount, $payment->method_currency) }}
                    </span>
                </div>
            </div>

        </div>
    </div>
    <div class="w-full lg:flex-1 bg-gray-100 dark:bg-gray-800 h-full sm:overflow-y-auto">
        <div class="px-4 my-4 flex flex-col gap-4 ">

            {{ $this->form }}

            <div>
                {{ $this->paymentAction }}
            </div>
        </div>
    </div>


    <x-filament-actions::modals />
</div>

