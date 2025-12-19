<?php

namespace Rapidez\CustomReorder;

use Illuminate\Support\ServiceProvider;

class CustomReorderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rapidez/custom_reorder.php', 'rapidez.custom_reorder');
    }

    public function boot()
    {
        $this
            ->bootViews()
            ->bootPublishables();
    }

    public function bootViews() : self
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rapidez-custom-reorder');

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
