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

class AdminDataTransactions extends Page implements HasTable

{

    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = "Transactions";
    protected static ?string $navigationLabel = "Data Transactions";
    protected static ?string $title = "Data Transactions";

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.admin-data-transactions';


    public static function canAccess(): bool
    {
       return auth()->user()->can_view_transactions;
    }
    
    public function table(Table $table): Table
{
    return $table
    ->query(
        // ...
        Transaction::where('type','data')->orderBy('id', 'desc')->limit(8)
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
        TextColumn::make('network')
            ->searchable()
            ->sortable()
            ->formatStateUsing(fn (string $state): string => strtoupper($state)) 
        ,
        TextColumn::make('plan_name')
            ->searchable()
            ->sortable()
            ->label('Plan'),
        TextColumn::make('user.email')
            ->sortable()
            ->searchable()
            ->copyable()
            ->copyMessage('Copied')
            ->toggleable()
        ,
        TextColumn::make('phone_number')
            ->label('Phone Number')
            ->searchable()
            ->sortable()
            ->copyable()
            ->copyMessage('Copied')
            ,
        TextColumn::make('amount')
            ->searchable()
            ,
        TextColumn::make('old_balance')->label('Old Balance'),
        TextColumn::make('new_balance')->label('New Balance'),
        TextColumn::make('cashback'),
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


        
        ]) ->filters([
            SelectFilter::make('status')
            ->options([
                'successful' => 'Successful',
                'failed' => 'Failed',
                'processing' => 'Processing',
                'rejected' => 'Rejected',
                'refund' => 'Refund',
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
