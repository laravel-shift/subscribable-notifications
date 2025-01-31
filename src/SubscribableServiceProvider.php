<?php

namespace YlsIdeas\SubscribableNotifications;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\Channels\MailChannel;
use Illuminate\Support\ServiceProvider;
use YlsIdeas\SubscribableNotifications\Channels\SubscriberMailChannel;

class SubscribableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'subscriber');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/subscriber'),
            ], 'subscriber-views');

            $this->publishes([
                __DIR__.'/../stubs/SubscribableServiceProvider.stub' => app_path('Providers/SubscribableServiceProvider.php'),
            ], 'subscriber-provider');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(MailChannel::class, SubscriberMailChannel::class);
        if (method_exists($this->app, 'scoped')) {
            $this->app->scoped(Subscriber::class, function (Application $app) {
                return new Subscriber($app);
            });
        } else {
            $this->app->singleton(Subscriber::class, function (Application $app) {
                /** @phpstan-ignore-next-line */
                return new Subscriber($app);
            });
        }
    }
}
