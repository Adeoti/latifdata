<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class MyElectricityTransactions extends Page implements HasTable
{

    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationGroup = "Transactions";
    protected static ?string $navigationLabel = "Electricity Transactions";

    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.customer.pages.my-electricity-transactions';


    public function table(Table $table): Table
{
    return $table
    ->query(
        // ...
        Transaction::where('user_id', auth()->id())->where('type','electricity')->orderBy('id', 'desc')->limit(8)
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
       
        
        TextColumn::make('meter_type')
            ->searchable()
            ->sortable()
            ->label('Meter Type'),
        TextColumn::make('meter_number')
            ->searchable()
            ->copyable()
            ->copyMessage('Copied')
            ->sortable()
            ->toggleable()
            ->label('Meter Number'),
        TextColumn::make('customer_name')
            ->searchable()
            ->sortable()
            ->toggleable()
            ->label('Customer\'s Name'),
        TextColumn::make('customer_address')
            ->searchable()
            ->sortable()
            ->toggleable()
            ->label('Customer\'s Address'),
        TextColumn::make('token_pin')
            ->searchable()
            ->sortable()
            ->copyable()
            ->copyMessage('Token copied')
            ->toggleable()
            ->label('Token'),
        TextColumn::make('disco_name')
            ->searchable()
            ->sortable()
            ->toggleable()
            ->label('Disco Name'),
       
        TextColumn::make('amount')
            ->searchable()
            ,
        TextColumn::make('charges')
        ,
        TextColumn::make('old_balance')->label('Old Balance'),
        TextColumn::make('new_balance')->label('New Balance'),
        
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
        ->actions([
            Action::make('Pop Message')
                ->color('info')
                ->icon('heroicon-m-bell-snooze')
                ->visible(function(Model $record){
                    if($record->status == 'successful'){
                        return true;
                    }
                })
                ->action(function(Model $record){
                    $this->dispatch(
                        'alert',
                        title: 'Successful',
                        text: $record->note,
                        type: 'success',
                        button: 'Got it!'
                    );
                })
        ])
        ->filters([
            SelectFilter::make('status')
            ->options([
                'successful' => 'Successful',
                'failed' => 'Failed',
                'pending' => 'Pending',
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

