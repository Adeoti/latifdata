<?php

namespace App\Filament\Customer\Resources\MyTransactionsResource\Widgets;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Transaction;
use Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class MyTransactionsWidget extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(
                // ...
                Transaction::where('user_id', auth()->id())->orderBy('id', 'desc')->limit(8)
            )
            ->paginated(true)
            ->columns([
                //
                TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable()
                    ,
                TextColumn::make('note')
                    ->markdown()
                    ->toggleable(),
                TextColumn::make('amount'),
                TextColumn::make('old_balance'),
                TextColumn::make('new_balance'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dated')
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
                SelectFilter::make('status')
                ->options([
                    'successful' => 'Successful',
                    'failed' => 'Failed',
                    'processing' => 'Processing',
                    'rejected' => 'Rejected',
                ]),
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
