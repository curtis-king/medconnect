<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test frais-inscriptions routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing frais-inscriptions routes...');

        $routes = [
            'frais-inscriptions.index',
            'frais-inscriptions.create',
            'frais-inscriptions.store',
            'frais-inscriptions.show',
            'frais-inscriptions.edit',
            'frais-inscriptions.update',
            'frais-inscriptions.destroy',
        ];

        foreach ($routes as $routeName) {
            try {
                $url = route($routeName, $routeName === 'frais-inscriptions.show' || $routeName === 'frais-inscriptions.edit' || $routeName === 'frais-inscriptions.update' || $routeName === 'frais-inscriptions.destroy' ? 1 : []);
                $this->line("✓ Route {$routeName}: {$url}");
            } catch (\Exception $e) {
                $this->error("✗ Route {$routeName}: {$e->getMessage()}");
            }
        }

        $this->info('Route testing completed.');
    }
}
