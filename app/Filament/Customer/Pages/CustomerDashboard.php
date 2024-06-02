<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Transaction;
use App\Models\Announcement;
use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Filament\Tables\Filters\Filter;
use Filament\Widgets\AccountWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Customer\Widgets\CustomerStats;

use Filament\Tables\Concerns\InteractsWithTable;

use App\Filament\Customer\Widgets\ServicesLinksWidget;
use App\Filament\Customer\Widgets\CustomMobileHomeScreen;
use App\Filament\Customer\Widgets\RecentActivitiesWidget;


class CustomerDashboard extends Page implements HasTable
{

    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.customer.pages.customer-dashboard';

    protected static ?string $navigationLabel = "Dashboard";
    protected  ?string $heading = "";
    protected static ?string $title = "Dashboard";
    protected static ?string $slug = "";


    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;


    public $announcement_status;
    public $announcement_content;
    public $announcement_style;
    public $user_balance;
    public $cashback_amount;

    public $balanceToggle;

    protected $listeners = ['toggleBalance'];

    public function mount()
    {
        $announcement = Announcement::where('is_active', true)->first();

        $this->user_balance = number_format(auth()->user()->balance,2);
        $this->cashback_amount = number_format(auth()->user()->cashback_balance,2);

        $this->balanceToggle = auth()->user()->balance_toggle;
            
        if ($announcement) {
            $this->announcement_status = $announcement->is_active;
            $this->announcement_style = $announcement->style;
            $this->announcement_content = $announcement->message;
        
        }
    }

    public function toggleBalance()
    {
        $user = auth()->user();
        $user->balance_toggle = !$user->balance_toggle;
        $user->save();

        $this->balanceToggle = $user->balance_toggle;
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //        // CustomMobileHomeScreen::class,
    //     ];
    // }

    // protected function getFooterWidgets(): array
    // {
    //     return [
            
    //         //AccountWidget::class,
    //         //FilamentInfoWidget::class,
    //         //CustomerStats::class,
    //        // RecentActivitiesWidget::class,
    //        // ServicesLinksWidget::class,
    //     ];
    // }




    public function table(Table $table): Table
    {
        return $table
            ->query(
                // ...
                Transaction::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(8)
            )
            ->paginated(false)
            ->columns([
                //
                TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Copied')
                    ->tooltip('Click to copy')
                    ,
                TextColumn::make('amount'),
                TextColumn::make('old_balance'),
                TextColumn::make('new_balance'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dated')
                    ->date("F d, Y h:i:s A")
                    ->sortable(),
                
                TextColumn::make('status')
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'successful' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'refund' => 'info',
                    })
                ,


                
            ])
            
            ->filters([
                Filter::make('created_at')
    ->form([
        DatePicker::make('created_from'),
        DatePicker::make('created_until'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    })
            ])
            
            ;
    }
   

    
}
