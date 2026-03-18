<?php

namespace Stevebauman\Purify;

use HTMLPurifier_DefinitionCacheFactory;
use Illuminate\Support\ServiceProvider;
use Stevebauman\Purify\Commands\ClearCommand;

class PurifyServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/purify.php', 'purify');

        $this->commands(ClearCommand::class);

        $this->app->singleton('purify', function ($app): \Stevebauman\Purify\PurifyManager {
            if ($cache = config('purify.serializer.cache')) {
                HTMLPurifier_DefinitionCacheFactory::instance()->register($cache, $cache);
            }

            return new PurifyManager($app);
        });
    }

    /**
     * Register the publishable configuration.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/purify.php' => config_path('purify.php'),
            ], 'config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['purify'];
    }
}
