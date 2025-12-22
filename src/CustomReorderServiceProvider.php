<?php

namespace Rapidez\CustomReorder;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Rapidez\CustomReorder\Http\ViewComposers\ConfigComposer;

class CustomReorderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rapidez/custom_reorder.php', 'rapidez.custom_reorder');
    }

    public function boot()
    {
        $this
            ->bootComposers()
            ->bootViews()
            ->bootPublishables();
    }

    protected function bootComposers(): static
    {
        View::composer('rapidez::layouts.app', ConfigComposer::class);

        return $this;
    }

    public function bootViews() : self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rapidez-reorder');

        return $this;
    }

    public function bootPublishables() : self
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/rapidez-custom-reorder'),
        ], 'rapidez-custom-reorder-views');

        $this->publishes([
            __DIR__.'/../config/rapidez/custom_reorder.php' => config_path('rapidez/custom_reorder.php'),
        ], 'rapidez-custom-reorder-config');

        return $this;
    }
}
