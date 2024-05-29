<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class AdminRecentActivities extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 8;
    protected static ?string $heading = 'Recent Activities';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // ...
                Transaction::orderBy('id', 'desc')->limit(8)
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
                    TextColumn::make('user.email')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Email Copied')
                    ->sortable()
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
            
            
            
            ;
    }
}
