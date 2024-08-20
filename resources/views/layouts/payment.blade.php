<!DOCTYPE html>

<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <title>{{ trans('filament-payments::messages.view.title_pay_page') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .peer:checked~.group .peer-checked\:group\:bg-teal-600 {
            background-color: #14B8A6;
            border: none;
        }

        .peer:checked~.group .peer-checked\:group\:visible {
            display: block;
        }
    </style>

    @yield('head')
</head>

<body class="bg-teal-50">
    {{-- Content --}}
    @yield('content')

    {{-- Script --}}
    @yield('script')
</body>

</html>
