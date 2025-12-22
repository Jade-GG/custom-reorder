<?php

namespace Rapidez\CustomReorder\Http\ViewComposers;

use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class ConfigComposer
{
    public function compose(View $view)
    {
        Config::set('frontend.custom_reorder', config('rapidez.custom_reorder'));
        Config::set('frontend.custom_reorder.add_selected', __('The selected products have been added to the cart.'));
    }
}
