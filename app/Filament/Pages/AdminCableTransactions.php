<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class AdminCableTransactions extends Page implements HasTable
{

    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationGroup = "Transactions";
    protected static ?string $navigationLabel = "Cable Transactions";
    protected static ?string $title = "Cable Transactions";

    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.pages.admin-cable-transactions';


    public function table(Table $table): Table
{
    return $table
    ->query(
        // ...
        Transaction::where('type','cable')->orderBy('id', 'desc')->limit(8)
    )
    ->columns([
        //
        TextColumn::make('reference_number')
            ->label('Reference')
            ->searchable()
            ->toggleable()
            ->copyable()
            ->copyMessage('Copied')
            ,

        TextColumn::make('note')
            ->markdown()
            ->toggleable(),
        TextColumn::make('user.email')
            ->sortable()
            ->searchable()
            ->copyable()
            ->copyMessage('Copied')
            ->toggleable()
        ,
        TextColumn::make('plan_name')
            ->searchable()
            ->label('Cable Name')
            ->sortable()
        ,
        TextColumn::make('customer_name')
            ->searchable()
            ->sortable()
            ->label('Customer'),
            
        TextColumn::make('cable_plan')
            ->searchable()
            ->sortable()
            ->label('Plan'),

        TextColumn::make('iuc_number')
            ->searchable()
            ->copyable()
            ->copyMessage('Copied')
            ->sortable()
            ->label('Cable Number'),
       
        TextColumn::make('amount')
            ->searchable()
            ,
       
        TextColumn::make('charges')
            ->searchable()
            ,

        TextColumn::make('old_balance')->label('Old Balance'),
        TextColumn::make('new_balance')->label('New Balance'),
        TextColumn::make('cashback'),
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


        
        ]) ->filters([
            SelectFilter::make('status')
            ->options([
                'successful' => 'Successful',
                'failed' => 'Failed',
                'processing' => 'Processing',
                'rejected' => 'Rejected',
            ]),
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
        ]);
}
}
