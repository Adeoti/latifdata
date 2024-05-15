<?php

namespace App\Filament\Customer\Widgets;

use Closure;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\PaymentIntegration;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class ManualFundingWidget extends BaseWidget
{


    protected int | string | array $columnSpan = 'full';


    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                PaymentIntegration::where('id', '>', 0)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('manual_bank_name')
                    ->label('Bank Name')
                    ,
                TextColumn::make('manual_account_name')
                    ->label('Account Name')
                ,
                TextColumn::make('manual_account_number')
                    ->label('Account Number')
                    ->copyable()
                    ->copyMessage('Account Number Copied')
                    ->tooltip('Click to copy account number')
                ,
            ]);
    }
}
