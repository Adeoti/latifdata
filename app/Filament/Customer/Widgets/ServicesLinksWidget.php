<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\Widget;

class ServicesLinksWidget extends Widget
{
    protected static string $view = 'filament.customer.widgets.services-links-widget';


    protected int | string | array $columnSpan = 'full';
}
