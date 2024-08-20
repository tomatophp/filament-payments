<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@lang('Deposit with Stripe')</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    @php
        $publishable_key = $data->publishable_key;
        $sessionId = $data->session;
    @endphp

    <script>
        "use strict";
        var stripe = Stripe('{{ $publishable_key }}');
        stripe.redirectToCheckout({
            sessionId: '{{ $sessionId }}'
        });
    </script>
</body>

</html>
