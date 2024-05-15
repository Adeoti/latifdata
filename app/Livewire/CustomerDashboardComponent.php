<?php

namespace App\Livewire;

use App\Models\Announcement;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;

use Livewire\Component;

class CustomerDashboardComponent extends Component
{








    

    public function render()
    {

        $announcement_status = $announcement_content = $announcement_style = "";

        $announcement = Announcement::where('is_active',true)->first();

           if(isset($announcement)){
            $announcement_status = $announcement->is_active;
            $announcement_style = $announcement->style;
            $announcement_content = $announcement->message;
           }
        
        return view('livewire.customer-dashboard-component',
        [
            'announcement_status' => $announcement_status,
            'announcement_style' => $announcement_style,
            'announcement_content' => $announcement_content,
        ]
    );
    }
}


