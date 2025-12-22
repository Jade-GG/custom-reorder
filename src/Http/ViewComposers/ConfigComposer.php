<?php

namespace Rapidez\CustomReorder\Http\ViewComposers;

use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class ConfigComposer
{
    public function compose(View $view)
    {
        Config::set('frontend.custom_reorder', config('rapidez.custom_reorder'));
    }
}
