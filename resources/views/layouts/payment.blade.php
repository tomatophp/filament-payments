@props([
    'livewire' => null,
])

    <!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="{{ trans('filament-payments::messages.view.contact_us') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{url()->current()}}" />
    <title>{{ trans('filament-payments::messages.view.title_pay_page') }}</title>

    <meta property="og:type" content="@yield('type', 'website')" />
    <meta property="og:title" content="{{ trans('filament-payments::messages.view.title_pay_page') }}" />
    <meta property="og:description" content="{{ trans('filament-payments::messages.view.contact_us') }}" />
    <meta property="og:image" content="{{ filament()->getBrandLogo() }}" />
    <meta property="og:image:alt" content="{{ trans('filament-payments::messages.view.title_pay_page') }}" />
    <meta property="og:url" content="{{url()->current()}}" />
    <meta property="og:site_name" content="{{config('app.name')}}" />

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ trans('filament-payments::messages.view.title_pay_page') }}">
    <meta name="twitter:description" content="{{ trans('filament-payments::messages.view.contact_us') }}">
    <meta name="twitter:image" content="{{ filament()->getBrandLogo() }}">

    @if ($favicon = filament()->getFavicon())
        <link rel="icon" href="{{ $favicon }}" />
    @endif

    <title>
        {{ filled($title = strip_tags(($livewire ?? null)?->getTitle() ?? '')) ? "{$title} - " : null }}
        {{ strip_tags(filament()->getBrandName()) }}
    </title>


    <style>
        [x-cloak=''],
        [x-cloak='x-cloak'],
        [x-cloak='1'] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            [x-cloak='-lg'] {
                display: none !important;
            }
        }

        @media (min-width: 1024px) {
            [x-cloak='lg'] {
                display: none !important;
            }
        }
    </style>

    @filamentStyles

    {{ filament()->getTheme()->getHtml() }}
    {{ filament()->getFontHtml() }}

    <style>
        :root {
            --font-family: '{!! filament()->getFontFamily() !!}';
            --sidebar-width: {{ filament()->getSidebarWidth() }};
            --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
            --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
        }
    </style>

    @stack('styles')

    @livewireStyles
</head>

<body
    {{ $attributes
            ->merge(($livewire ?? null)?->getExtraBodyAttributes() ?? [], escape: false)
            ->class([
                'fi-body',
                'fi-panel-' . filament()->getId(),
                'min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white',
            ]) }}
>

@livewire(Filament\Livewire\Notifications::class)

{!! $content !!}

@filamentScripts(withCore: true)

@if (config('filament.broadcasting.echo'))
    <script data-navigate-once>
        window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

        window.dispatchEvent(new CustomEvent('EchoLoaded'))
    </script>
@endif

@stack('scripts')
@stack('modals')

@livewireScripts
</body>
</html>
