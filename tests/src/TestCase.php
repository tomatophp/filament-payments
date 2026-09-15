<?php

namespace TomatoPHP\FilamentPayments\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Panel;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Translatable\TranslatableServiceProvider;
use TomatoPHP\FilamentPayments\FilamentPaymentsServiceProvider;
use TomatoPHP\FilamentPayments\Tests\Models\User;
use TomatoPHP\FilamentTranslationComponent\FilamentTranslationComponentServiceProvider;

#[WithEnv('DB_CONNECTION', 'testing')]
// The payment page is protected by `auth:{filament-payments.guard}`.
#[WithConfig('filament-payments.guard', 'testing')]
abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;
    use WithWorkbench;

    public ?Panel $panel;

    protected function getPackageProviders($app): array
    {
        $providers = [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            SchemasServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            MediaLibraryServiceProvider::class,
            TranslatableServiceProvider::class,
            FilamentTranslationComponentServiceProvider::class,
            FilamentPaymentsServiceProvider::class,
            AdminPanelProvider::class,
        ];

        sort($providers);

        return $providers;
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config) {
            $config->set('database.default', 'testing');
            $config->set('database.connections.testing', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);

            $config->set('auth.guards.testing.driver', 'session');
            $config->set('auth.guards.testing.provider', 'testing');
            $config->set('auth.providers.testing.driver', 'eloquent');
            $config->set('auth.providers.testing.model', User::class);

            $config->set('filament-payments.guard', 'testing');

            $config->set('view.paths', [
                ...$config->get('view.paths'),
                __DIR__.'/../resources/views',
            ]);
        });
    }
}
