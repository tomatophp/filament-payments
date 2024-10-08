<?php

namespace TomatoPHP\FilamentPayments\Console;

use Illuminate\Console\Command;
use TomatoPHP\ConsoleHelpers\Traits\RunCommand;
use TomatoPHP\FilamentPayments\Facades\FilamentPayments;

class FilamentPaymentsInstall extends Command
{
    use RunCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'filament-payments:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install package and publish assets';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Publish Vendor Assets');
        $this->callSilent('optimize:clear');
        $this->artisanCommand(["migrate"]);
        $this->artisanCommand(["optimize:clear"]);
        FilamentPayments::loadDrivers();
        $this->info('Filament Payments installed successfully.');
    }
}
