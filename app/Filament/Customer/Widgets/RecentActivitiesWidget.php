<?php

namespace App\Filament\Customer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaction;
use Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class RecentActivitiesWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
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
                    })
                ,


                
            ])
            
            ->filters([
                Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('created_from'),
        Forms\Components\DatePicker::make('created_until'),
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
