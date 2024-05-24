<?php

namespace App\Filament\Customer\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Transaction;
use Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Customer\Resources\MyTransactionsResource\Widgets\MyTransactionsWidget;
use App\Filament\Customer\Resources\MyTransactionsResource\Widgets\CustomerTransactionsWidget;

class CustomerTransactions extends Page implements HasTable
{

    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = "All Transactions";
    protected static ?string $title = "My Transactions";

    protected static ?string $navigationGroup = "Transactions";
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.customer.pages.customer-transactions';



//     protected function getHeaderWidgets(): array
// {
//     return [
//        MyTransactionsWidget::class
//     ];
// }


public function table(Table $table): Table
    {
        $ngn = "₦";
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
                    ->copyable()
                    ->copyMessage('Copied')
                    ,
                TextColumn::make('note')
                    ->markdown()
                    ->toggleable(),
                TextColumn::make('amount')
                ->default($ngn."00.00")
                
                ,
                TextColumn::make('old_balance')
                ->default($ngn."00.00")
                ,
                TextColumn::make('new_balance')
                ->default($ngn."00.00")
                ,
                TextColumn::make('cashback')->label('Cashback')
                ->default($ngn."00.00")
                ,
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
            // ->actions([
            //     Action::make('Pop Message')
            //         ->color('info')
            //         ->icon('heroicon-m-bell-snooze')
            //         ->visible(function(Model $record){
            //             if($record->status == 'successful'){
            //                 return true;
            //             }
            //         })
            //         ->action(function(Model $record){
            //             $this->dispatch(
            //                 'alert',
            //                 title: 'Successful',
            //                 text: $record->note,
            //                 type: 'success',
            //                 button: 'Got it!'
            //             );
            //         })
            // ])
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
