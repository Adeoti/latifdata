<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Transaction;
use Forms\Components\DatePicker;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Concerns\InteractsWithTable;

class AdminAllTransactions extends Page implements HasTable
{

    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = "All Transactions";
    protected static ?string $title = "All Transactions";

    protected static ?string $navigationGroup = "Transactions";
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.admin-all-transactions';



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
                Transaction::orderBy('id', 'desc')->limit(2)
            )
            ->paginated(true)
            ->columns([
                //
                TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copied')
                    ->toggleable()
                    ,
                TextColumn::make('note')
                    ->markdown()
                    ->toggleable(),
                TextColumn::make('user.email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copied')
                    ->sortable()
                ,
                TextColumn::make('amount'),
                TextColumn::make('old_balance'),
                TextColumn::make('new_balance'),
                TextColumn::make('cashback'),
                TextColumn::make('amount_paid'),
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
            ->actions([
                Action::make('refund')
                ->color('danger')
                ->icon('heroicon-m-receipt-refund')
                ->openUrlInNewTab()
                ->visible(function(Model $record){
                    if($record->status == 'pending'){
                        return true;
                    }
                })
                ->action(function(Model $record){

                    $ngn = "₦";

                    $record_id = $record->id;
                    $record_old_balance = $record->old_balance;
                    $record_new_balance = $record->new_balance;
                    $record_cashback = $record->cashback;
                    $record_amount = $record->amount;
                    $record_amount_paid = $record->amount_paid;
                    $record_user = $record->user_id;

                    //User info

                    $user_balance = User::find($record_user)->balance;
                    $user_cashback_balance = User::find($record_user)->cashback_balance;



                    //Update User's Balance to ==== [user_balance + record_amount OR amount_paid]
                    //Update User's Cashback Balance if there is any recorded cashback 
                    //Update Transaction's New Balance to ==== [old_balance]
                    //Update Transaction's Cashback  to ==== [00.00]
                    //Update Transaction's status to ==== [failed]


                    $record_amount = str_replace(['₦', ','], '', $record_amount);
                    $record_amount = (float) $record_amount;
                    
                    $record_old_balance = str_replace(['₦', ','], '', $record_old_balance);
                    $record_old_balance = (float) $record_old_balance;
                    
                    $record_new_balance = str_replace(['₦', ','], '', $record_new_balance);
                    $record_new_balance = (float) $record_new_balance;
                    
                    $record_amount_paid = str_replace(['₦', ','], '', $record_amount_paid);
                    $record_amount_paid = (float) $record_amount_paid;
                    
                    $record_cashback = str_replace(['₦', ','], '', $record_cashback);
                    $record_cashback = (float) $record_cashback;


                    $new_user_balance = $new_cashback_balance = 0;

                    if($record_amount_paid > 0){
                        $new_user_balance = (double)$user_balance + (double)$record_amount_paid;
                    }else{
                        $new_user_balance = (double)$user_balance + (double)$record_amount;
                    }
                    
                   

                    if($record_cashback > 0){
                        $new_cashback_balance = (double)$user_cashback_balance - (double)$record_cashback;
                    }else{
                        $new_cashback_balance = $user_cashback_balance;
                    }

                    
                    //Now Update the User's Balance and cashback balance...
                    //.............
                    DB::table('users')
                    ->where('id', $record_user)
                    ->update([
                        'balance' => $new_user_balance,
                        'cashback_balance' => $new_cashback_balance
                    ]);

                    //...Now update the transaction record

                    DB::table('transactions')
                    ->where('id', $record_id)
                    ->update([
                        'new_balance' => "$ngn".number_format($record_old_balance,2),
                        'cashback' => "$ngn"."00.00",
                        'amount_paid' => "$ngn"."00.00",
                        'status' => 'failed'
                    ]);








                    //dd("Record Amount = ".$record_amount." Old Balance = ".$record_old_balance." New Balance = ".$record_new_balance." Amount Paid = ".$record_amount_paid." Cashback = ".$record_cashback);


                    $this->dispatch('alert',
                        title:'Refunded',
                        type:'success',
                        text:"You've successfully refunded this transaction!",
                        button:'Great!'
                        );



                })
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
