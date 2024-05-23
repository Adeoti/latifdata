<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Markdown;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = "Users";
    protected static ?string $navigationGroup = "User Mgt.";
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Basic Info')->schema([
                    TextInput::make('name')
                        ->label('Fullname')
                        ->required(),
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true)
                    ,
                    
                    TextInput::make('username'),
                    TextInput::make('phone_number'),
                    TextInput::make('password')
                    ->hiddenOn('edit')
                    ->password()
                    ->required()
                    ->revealable(),
                    TextInput::make('position')
                    ->required()
                    
                    
                ])->columnSpan(2),
                 
                Section::make('Meta')->schema([
                    FileUpload::make('profile_image')
                    ->disk('public')
                    ->directory('user_images')
                    ->imageEditor()
                    ->image(),
                    
                    
                    Section::make('')->schema([
                        Toggle::make('user_status')
                        ->label('Unactive / Active')
                        ->inline(false)
                        ,
                        Toggle::make('is_staff')
                        ->default(true)
                        ->label('Is Staff?')
                        ->inline(false)
                        ,
                    ])->columns(2),
                    RichEditor::make('address')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                ]) -> columnSpan(1),

                Section::make('Customer-related Info')->schema([
                   
                    Section::make('Transaction Auth')->schema([
                        Select::make('package')
                        ->options([
                            'primary' => 'primary',
                            'agent' => 'Agent',
                            'special' => 'Special',
                            'api' => 'API'
                        ]),
                        TextInput::make('referral_code'),
                        TextInput::make('transaction_pin')
                            ->numeric()
                            ->maxLength(4)
                    
                    ])->columnSpan(2),
                    Section::make('KYC')->schema([
                        TextInput::make('bvn')
                        ->numeric(),
                        TextInput::make('nin')
                            ->numeric()
                        ,
                        DatePicker::make('bvn_date_of_birth')
                            ,
                            Hidden::make('user_id')->default(Auth()->id())
                    ])->columnSpan(2),

                    Section::make('Automated Accounts')->schema([
                        TextInput::make('monniepoint_acct')->numeric(),
                        TextInput::make('wema_acct')->numeric(),
                        TextInput::make('sterling_acct')->numeric(),
                        TextInput::make('gtb_acct')->numeric(),
                        TextInput::make('providus_acct')->numeric(),
                        TextInput::make('rehoboth_acct')->numeric(),
                        TextInput::make('fidelity_acct')->numeric(),
                        TextInput::make('paystack_acct')->numeric(),
                        TextInput::make('flutterwave_acct')->numeric(),
                    ])->columnSpan(4)
                    ->collapsible()
                    ->collapsed(true)

                    
                ])
                    ->collapsible()
                    ->columns(4)
                    ->collapsed(true),
                
                //Permissions Form Section
                Section::make('Permissions')
                ->description('Set the allowed permissions for this account.')
                ->schema([
                    
                    Fieldset::make('Users Permissions')->schema([
                        Toggle::make('add_user'),
                        Toggle::make('edit_user'),
                        Toggle::make('view_user'),
                        Toggle::make('delete_user'),
                    ])->columns(4),
                   
                    Fieldset::make('Expenses Permissions')->schema([
                    Toggle::make('add_expenses'),
                    Toggle::make('edit_expenses'),
                    Toggle::make('view_expenses'),
                    Toggle::make('delete_expenses'),
                    ])->columns(4),
                    
                    Fieldset::make('Savings Permissions')->schema([
                    Toggle::make('add_savings'),
                    Toggle::make('edit_savings'),
                    Toggle::make('view_savings'),
                    Toggle::make('delete_savings'),
                    ])->columns(4),

                    Fieldset::make('Customers Permissions')->schema([
                    Toggle::make('add_customer'),
                    Toggle::make('edit_customer'),
                    Toggle::make('view_customer'),
                    Toggle::make('delete_customer'),
                    ])->columns(4),

                    Fieldset::make('Actions Permissions [can]')->schema([
                    Toggle::make('can_announcement'),
                    Toggle::make('can_private_message'),
                    Toggle::make('can_view_transactions'),
                    Toggle::make('can_manage_services'),
                    Toggle::make('can_upgrade_customer'),

                    Toggle::make('can_reset_password'),
                    Toggle::make('can_credit_customer'),
                    Toggle::make('can_set_price'),
                    Toggle::make('widget_balance'),
                    Toggle::make('widget_user_balance'),

                    Toggle::make('widget_savings'),
                    Toggle::make('widget_expenses'),
                    Toggle::make('widget_refund'),
                    Toggle::make('widget_cashflow'),
                    Toggle::make('widget_sales'),

                    Toggle::make('toggle_payment_method'),
                    Toggle::make('set_charges'),
                    Toggle::make('set_cashback'),
                    Toggle::make('set_referral'),
                    ])->columns(4),


                ])
                ->collapsible()
                ->collapsed()
                ,




            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    -> sortable()
                    -> searchable()
                    -> toggleable()
                    
                ,
                
                TextColumn::make('email')
                    -> sortable()
                    -> searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                ,

                TextColumn::make('phone_number')
                    -> sortable()
                    ->copyable()
                    ->copyMessage('Phone number copied')
                    -> searchable()
                    ->toggleable()
                ,

                TextColumn::make('username'),

                TextColumn::make('position')
                    -> sortable()
                    -> searchable()
                    -> toggleable()
                ,
                ToggleColumn::make('user_status')
                    ->label('Active Status')
                    ->sortable()
                    ->toggleable()
            ])
            ->filters([
                //
                
                SelectFilter::make('is_staff')
                ->label('Choose Category')
                ->options([
                    '1' => 'Staff',
                    '0' => 'Customers',
                ]),
            ])        ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
