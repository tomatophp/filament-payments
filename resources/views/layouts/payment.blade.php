<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <title>{{ trans('filament-payments::messages.view.title_pay_page') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    @filamentStyles

    <script src="https://unpkg.com/akar-icons-fonts"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

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

<body class="bg-teal-50 antialiased">
    {{-- Content --}}
    @yield('content')

    @livewire('notifications')

    @filamentScripts
</body>

</html>
