@extends('filament-payments::layouts.payment')

@section('head')
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .hidden {
            display: none;
        }

        #payment-message {
            color: rgb(105, 115, 134);
            font-size: 16px;
            line-height: 20px;
            padding-top: 12px;
            text-align: center;
        }

        #payment-element {
            margin-bottom: 24px;
        }

        /* Buttons and links */
        button {
            background: #5469d4;
            font-family: Arial, sans-serif;
            color: #ffffff;
            border-radius: 4px;
            border: 0;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: block;
            transition: all 0.2s ease;
            box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
            width: 100%;
        }

        button:hover {
            filter: contrast(115%);
        }

        button:disabled {
            opacity: 0.5;
            cursor: default;
        }

        /* spinner/processing state, errors */
        .spinner,
        .spinner:before,
        .spinner:after {
            border-radius: 50%;
        }

        .spinner {
            color: #ffffff;
            font-size: 22px;
            text-indent: -99999px;
            margin: 0px auto;
            position: relative;
            width: 20px;
            height: 20px;
            box-shadow: inset 0 0 0 2px;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
        }

        .spinner:before,
        .spinner:after {
            position: absolute;
            content: "";
        }

        .spinner:before {
            width: 10.4px;
            height: 20.4px;
            background: #5469d4;
            border-radius: 20.4px 0 0 20.4px;
            top: -0.2px;
            left: -0.2px;
            -webkit-transform-origin: 10.4px 10.2px;
            transform-origin: 10.4px 10.2px;
            -webkit-animation: loading 2s infinite ease 1.5s;
            animation: loading 2s infinite ease 1.5s;
        }

        .spinner:after {
            width: 10.4px;
            height: 10.2px;
            background: #5469d4;
            border-radius: 0 10.2px 10.2px 0;
            top: -0.1px;
            left: 10.2px;
            -webkit-transform-origin: 0px 10.2px;
            transform-origin: 0px 10.2px;
            -webkit-animation: loading 2s infinite ease;
            animation: loading 2s infinite ease;
        }

        @-webkit-keyframes loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes loading {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @media only screen and (max-width: 600px) {
            form {
                width: 80vw;
                min-width: initial;
            }
        }
    </style>
@endsection

@section('content')
    <div class="flex flex-col items-center w-full min-h-screen p-4">
        <div class="flex flex-col md:flex-row w-full max-w-5xl gap-4">
            <div class="bg-white rounded-lg shadow-md p-6 w-full md:w-2/3">
                {{-- Display a payment form --}}
                <form id="payment-form">
                    <div id="payment-element">
                        {{-- Stripe.js injects the Payment Element --}}
                    </div>
                    <button id="submit"
                        class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-teal-600 text-white hover:bg-teal-700 focus:outline-none focus:bg-teal-700 disabled:opacity-50 disabled:pointer-events-none">
                        <div class="spinner hidden" id="spinner"></div>
                        <span id="button-text">{{ trans('filament-payments::messages.view.pay_now') }}</span>
                    </button>
                    <div id="payment-message" class="hidden"></div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 w-full md:w-1/3">
                <div class="flex flex-col items-center">
                    @if($payment->model->getFirstMediaUrl('avatar'))
                        <img src="{{ $payment->model->getFirstMediaUrl('avatar') }}" alt="Logo" class="mb-4" width="100" height="100" class="rounded-full" style="aspect-ratio: 100 / 100; object-fit: cover;" />
                    @endif
                    <h2 class="text-xl font-bold mb-2">{{ $payment->model->name }}</h2>
                    <h3 class="text-lg font-semibold mb-4">{{ $payment->detail }}</h3>
                    <div class="w-full border-t border-gray-200 my-4"></div>
                    <div class="flex justify-between w-full mb-2">
                        <span class="text-gray-600">{{ trans('filament-payments::messages.view.amount') }}</span>
                        <span class="text-gray-800">{{ Number::currency($payment->amount, $payment->method_currency) }}</span>
                    </div>
                    <div class="flex justify-between w-full mb-2">
                        <span class="text-gray-600">{{ trans('filament-payments::messages.view.payment_gateway_fee') }}</span>
                        <span class="text-gray-800" id="feeAmount">{{ Number::currency($payment->charge, $payment->method_currency) }}</span>
                    </div>
                    <div class="w-full border-t border-gray-200 my-4"></div>
                    <div class="flex justify-between w-full">
                        <span class="text-lg font-bold">{{ trans('filament-payments::messages.view.total') }}</span>
                        <span class="text-2xl font-bold text-black" id="totalAmount">{{ Number::currency($payment->amount + $payment->charge, $payment->method_currency) }}</span>
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


    <script>
        const stripe = Stripe("{{ $data->publishable_key }}");
        const clientSecret = "{{ $data->client_secret }}";
        const successUrl = "{{ $data->success_url }}";

        let elements;

        initialize();
        checkStatus();

        document
            .querySelector("#payment-form")
            .addEventListener("submit", handleSubmit);

        // Fetches a payment intent and captures the client secret
        async function initialize() {
            elements = stripe.elements({
                clientSecret
            });

            const paymentElementOptions = {
                layout: "tabs",
            };

            const paymentElement = elements.create("payment", paymentElementOptions);
            paymentElement.mount("#payment-element");
        }

        async function handleSubmit(e) {
            e.preventDefault();
            setLoading(true);

            const {
                error
            } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: successUrl,
                },
            });

            if (error.type === "card_error" || error.type === "validation_error") {
                showMessage(error.message);
            } else {
                showMessage("An unexpected error occurred.");
            }

            setLoading(false);
        }

        // Fetches the payment intent status after payment submission
        async function checkStatus() {
            const clientSecret = new URLSearchParams(window.location.search).get(
                "payment_intent_client_secret"
            );

            if (!clientSecret) {
                return;
            }

            const {
                paymentIntent
            } = await stripe.retrievePaymentIntent(clientSecret);

            switch (paymentIntent.status) {
                case "succeeded":
                    showMessage("Payment succeeded!");
                    break;
                case "processing":
                    showMessage("Your payment is processing.");
                    break;
                case "requires_payment_method":
                    showMessage("Your payment was not successful, please try again.");
                    break;
                default:
                    showMessage("Something went wrong.");
                    break;
            }
        }

        // ------- UI helpers -------

        function showMessage(messageText) {
            const messageContainer = document.querySelector("#payment-message");

            messageContainer.classList.remove("hidden");
            messageContainer.textContent = messageText;

            setTimeout(function() {
                messageContainer.classList.add("hidden");
                messageContainer.textContent = "";
            }, 4000);
        }

        // Show a spinner on payment submission
        function setLoading(isLoading) {
            if (isLoading) {
                // Disable the button and show a spinner
                document.querySelector("#submit").disabled = true;
                document.querySelector("#spinner").classList.remove("hidden");
                document.querySelector("#button-text").classList.add("hidden");
            } else {
                document.querySelector("#submit").disabled = false;
                document.querySelector("#spinner").classList.add("hidden");
                document.querySelector("#button-text").classList.remove("hidden");
            }
        }
    </script>
@endsection
