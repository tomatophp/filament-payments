<?php

use Filament\Support\Facades\FilamentAsset;

/**
 * Filament v5 panels only compile the utilities Filament itself uses, so every utility class the
 * payment views rely on must be defined in the stylesheet the package ships.
 */
it('registers the payments stylesheet with filament', function () {
    $styles = collect(FilamentAsset::getStyles(['tomatophp/filament-payments']))
        ->map(fn ($asset) => $asset->getId());

    expect($styles)->toContain('filament-payments');
});

it('ships every utility class the payment views use', function () {
    $css = file_get_contents(__DIR__.'/../../resources/dist/filament-payments.css');

    // Selectors like `.md\:w-1\/3 {`, `.hover\:bg-teal-700:hover {` or `.dark\:text-white {`.
    $defined = collect(preg_match_all('/\.((?:[a-z0-9-]|\\\\[:\/])+)(?=[\s{,:])/i', $css, $matches) ? $matches[1] : [])
        ->map(fn (string $selector) => stripslashes($selector))
        ->all();

    // Filament component classes and the Stripe spinner (styled by the Stripe view itself) are not utilities.
    $ignored = '/^(fi-|spinner$)/';

    $used = collect(glob(__DIR__.'/../../resources/views/{,*/}*.blade.php', GLOB_BRACE))
        ->flatMap(function (string $view) {
            preg_match_all('/class="([^"]*)"/', file_get_contents($view), $found);

            return collect($found[1])->flatMap(fn (string $classes) => preg_split('/\s+/', $classes));
        })
        ->reject(fn (string $class) => $class === '' || preg_match('/[{}@$]/', $class))
        ->reject(fn (string $class) => preg_match($ignored, $class))
        ->unique()
        ->values();

    expect($used)->not->toBeEmpty()
        ->and($used->diff($defined)->values()->all())->toBe([]);
});
