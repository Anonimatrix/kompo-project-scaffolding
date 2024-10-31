<?php

namespace Condoedge\GenericPackageName;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class GenericPackageNameServiceProvider extends ServiceProvider
{
    use \Kompo\Routing\Mixins\ExtendsRoutingTrait;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadHelpers();

        $this->extendRouting();

        $this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'generic-package-name');

        //Usage: php artisan vendor:publish --tag="generic-package-name-config"
        $this->publishes([
            __DIR__.'/../config/generic-package-name.php' => config_path('generic-package-name.php'),
        ], 'generic-package-name-config');

        $this->loadConfig();

        $this->loadCrons();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Best way to load routes. This ensures loading at the very end (after fortifies' routes for ex.)
        $this->booted(function () {
            \Route::middleware('web')->group(__DIR__.'/../routes/web.php');
        });
    }

    protected function loadHelpers()
    {
        $helpersDir = __DIR__.'/Helpers';

        $autoloadedHelpers = collect(\File::allFiles($helpersDir))->map(fn($file) => $file->getRealPath());

        $autoloadedHelpers->each(function ($path) {
            if (file_exists($path)) {
                require_once $path;
            }
        });
    }

    protected function loadConfig()
    {
        $dirs = [
            'generic-package-name' => __DIR__.'/../config/generic-package-name.php',
        ];

        foreach ($dirs as $key => $path) {
            $this->mergeConfigFrom($path, $key);
        }
    }

    protected function loadCrons()
    {
        $schedule = $this->app->make(Schedule::class);
    }
}
