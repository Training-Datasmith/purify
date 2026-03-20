<?php

declare (strict_types=1);
namespace Stevebauman\Purify;

use Html_Purifier_definition_Cache_Factory;
use Illuminate\Support\Service_Provider;
use Stevebauman\Purify\Commands\Clear_Command;
class Purify_Service_Provider extends Service_Provider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->merge_config_from(__DIR__ . '/../config/purify.php', 'purify');
        $this->commands(Clear_Command::class);
        $this->app->singleton('purify', function ($app): \Stevebauman\Purify\Purify_Manager {
            if ($cache = config('purify.serializer.cache')) {
                Html_Purifier_definition_Cache_Factory::instance()->register($cache, $cache);
            }
            return new Purify_Manager($app);
        });
    }
    /**
     * Register the publishable configuration.
     */
    public function boot(): void
    {
        if ($this->app->running_in_console()) {
            $this->publishes([__DIR__ . '/../config/purify.php' => config_path('purify.php')], 'config');
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