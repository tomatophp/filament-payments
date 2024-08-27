<div class="flex flex-col items-center w-full min-h-screen p-4">
    <div class="flex flex-col md:flex-row w-full max-w-5xl gap-4">
        <div class="bg-white rounded-lg shadow-md p-6 w-full md:w-2/3">
            <form wire:submit.prevent="process">
                <div class="flex items-center justify-between mb-6">
                    @php
                        if(!str($userIp)->contains('127.0.0.1')){
                            $response = Http::get("http://ip-api.com/json/{$userIp}");
                            $data = $response->json();
                            $country = $data['status'] == 'fail' ? 'none' : $data['countryCode'];
                        }
                        else {
                            $country = 'us';
                        }

                    @endphp

                    <span class="text-lg font-semibold">{{ trans('filament-payments::messages.view.choose_payment_method') }}</span>
                    <img src="https://cdn.jsdelivr.net/gh/hampusborgos/country-flags@main/svg/{{ strtolower($country) }}.svg" alt="Flag" width="24" height="24" />
                </div>
                <div class="space-y-4">
                    @forelse ($gateways as $gateway)
                        <div>
                            <input class="sr-only peer" type="radio" wire:model="selectedGateway" wire:change="calculateFee"
                                id="gateway-{{ $gateway->id }}" value="{{ $gateway->id }}">
                            <label
                                class="flex items-center h-20 px-8 border rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:bg-teal-50 peer-checked:border-teal-600 group"
                                for="gateway-{{ $gateway->id }}">
                                <div
                                    class="flex items-center justify-center w-6 h-6 border border-gray-600 rounded-full peer-checked:group:bg-teal-600">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="hidden w-4 h-4 text-teal-200 fill-current peer-checked:group:visible"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @if ($gateway->getFirstMediaUrl('image'))
                                    <img src="{{ $gateway->getFirstMediaUrl('image') }}" alt="{{ $gateway->name }}"
                                        class="ltr:ml-6 rtl:mr-6" style="height: 32px;" />
                                @endif
                                <div class="flex flex-col ltr:ml-6 rtl:mr-6">
                                    @if ($gateway->name && !$gateway->getFirstMediaUrl('image'))
                                        <span class="text-xl font-medium">{{ $gateway->name }}</span>
                                    @endif
                                    @if ($gateway->description)
                                        <span class="text-sm font-light text-gray-800">{{ $gateway->description }}</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @empty
                        <p>{{ trans('filament-payments::messages.view.no_gateways_available') }}</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-4">
                    <button type="submit"
                        class="w-full py-3 px-4 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-teal-500 text-white hover:bg-teal-600 focus:outline-none focus:bg-teal-600 disabled:opacity-50 disabled:pointer-events-none">
                        {{ trans('filament-payments::messages.view.choose_payment_method') }}
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 w-full md:w-1/3">
            <div class="flex flex-col items-center">
<<<<<<< Updated upstream
{{--                @if ($payment->model->getFirstMediaUrl('avatar'))--}}
{{--                    <img src="{{ $payment->model->getFirstMediaUrl('avatar') }}" alt="Logo" class="mb-4"--}}
{{--                        width="100" height="100" class="rounded-full"--}}
{{--                        style="aspect-ratio: 100 / 100; object-fit: cover;" />--}}
{{--                @endif--}}
=======
                @if(method_exists($payment->model, 'hasMedia') && $payment->model->hasMedia('avatar'))
                    <img src="{{ $payment->model->getFirstMediaUrl('avatar') }}" alt="Logo" class="mb-4" width="100" height="100" class="rounded-full" style="aspect-ratio: 100 / 100; object-fit: cover;" />
                @endif
>>>>>>> Stashed changes
                <h2 class="text-xl font-bold mb-2">{{ $payment->model->name }}</h2>
                <h3 class="text-lg font-semibold mb-4">{{ $payment->detail }}</h3>
                <div class="w-full border-t border-gray-200 my-4"></div>
                <div class="flex justify-between w-full mb-2">
                    <span class="text-gray-600">{{ trans('filament-payments::messages.view.amount') }}</span>
                    <span class="text-gray-800">{{ Number::currency($payment->amount, $payment->method_currency) }}</span>
                </div>
                <div class="flex justify-between w-full mb-2">
                    <span class="text-gray-600">{{ trans('filament-payments::messages.view.payment_gateway_fee') }}</span>
                    <span class="text-gray-800">{{ Number::currency($payment->charge, $payment->method_currency) }}</span>
                </div>
                <div class="w-full border-t border-gray-200 my-4"></div>
                <div class="flex justify-between w-full">
                    <span class="text-lg font-bold">{{ trans('filament-payments::messages.view.total') }}</span>
                    <span class="text-2xl font-bold text-black">
                        {{ Number::currency($payment->final_amount, $payment->method_currency) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6 w-full max-w-5xl mt-4">
        <p class="text-center text-sm text-gray-600">
            {{ trans('filament-payments::messages.view.contact_us') }}
        </p>
    </div>
</div>
