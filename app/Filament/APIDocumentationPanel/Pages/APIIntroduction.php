<?php

namespace App\Filament\APIDocumentationPanel\Pages;

use Carbon\Carbon;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\MobileData;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Http;

class APIIntroduction extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    
    protected static ?string $navigationLabel = "Auth";
    protected static ?string $title = "API Auth";

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.a-p-i-documentation-panel.pages.a-p-i-introduction';



    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill(
        auth()->user()->attributesToArray()
    );
    }
   

    public $ngn = "₦";

     // Function to generate a random alphanumeric string
    private function generateRandomString($length = 10)
     {
         $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#$##$Bahqyieroipfmkbvfjkgriudbhchjks';
         $randomString = '';
 
         for ($i = 0; $i < $length; $i++) {
             $randomString .= $characters[rand(0, strlen($characters) - 1)];
         }
 
         return $randomString;
     }

    public function form(Form $form): Form
    {
        $user_balance = number_format(auth()->user()->balance,2);
        $ngn = "₦";

        return $form
            ->schema([
                Section::make('Authentication')
                    ->schema([
                        TextInput::make('api_key')
                            ->required()
                            ->password()
                            ->label('API Key')
                            ->revealable()
                            ->suffixAction(
                                Action::make('generateNewKey')
                                    ->icon('heroicon-m-receipt-refund')
                                    ->requiresConfirmation()
                                    ->tooltip('Generate New Key')
                                    ->action(function (Set $set, $state, Callable $get) {
                                       
                                        date_default_timezone_set('Africa/Lagos');
                                        $currentDateTime = Carbon::now();

                                        $formattedDateTime = $currentDateTime->format('YmdHi');
                                        $randomString = $this->generateRandomString(10);
                                        $requestId = $formattedDateTime . $randomString;
                                
                                        if(strlen($requestId) < 12) {
                                            $requestId .= $this->generateRandomString(12 - strlen($requestId));
                                        }

                                        $stragechars = "SWEETBILLFROMADEOTICODEDINIBADANNIGERIA05042024";
                                        $shuffled = str_shuffle($stragechars);
                                        $shuffled_sliced = substr($shuffled,0,8);

                                        $requestId = $requestId.uniqid().$shuffled_sliced.uniqid()."SWEETBILL".uniqid();
                                        $requestId_b = $requestId.uniqid().$shuffled_sliced.uniqid()."SWEETBILL".uniqid();

                                        $requestId_c = str_shuffle($requestId_b);
                                        $apikey_new = $requestId.$requestId_c.$formattedDateTime;

                                        DB::table('users')
                                        ->where('id', auth()->id())
                                        ->update([
                                            'api_key' => $apikey_new
                                        ]);
                                        
                                        $set('api_key',$apikey_new);

                                        

                                        $this->dispatch(
                                            'alert',
                                            type:'success',
                                            title:'Successful',
                                            text:"You've successfully generated a new API key",
                                            button:'Great!'
                                        );


                                    }),
                                )
                           
                                ]),
                      
                   
            ])
            ->statePath('data');
    }

    







}
