<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class UpgradeNDowngrade extends Component implements HasForms
{


    

    use InteractsWithForms;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    Select::make('email')
                        ->searchable()
                        ->required()
                        ->columnSpan(1)
                        ->options(User::all()->pluck('email','email'))
                        ,
                    Select::make('package')
                        ->options([
                            'primary' => 'Primary',
                            'agent' => 'Agent',
                            'special' => 'Special',
                            'api' => 'API'
                        ])
                        ->required()
                        ->columnSpan(1),
                    Hidden::make('user_id')->default(Auth()->id()),
                    RichEditor::make('note')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull()
                ])->columns(2)

            ])
            ->statePath('data');
    }



    public function upgradeNdowngrade(): void
    {

        $user_email = $this->form->getState()['email'];
        $package = $this->form->getState()['package'];
        $note = $this->form->getState()['note'];


        $ref_number = date('YmdHis') . uniqid();
        $ref_number = "Package_".$ref_number;
    
        //Check if this user is actually a staff!

        $me = DB::table('users')->where('id',auth()->id())->first();

        if($me){

        // Retrieve the user record
        $user = DB::table('users')->where('email', $user_email)->first();
    
        if ($user) {
            // Update the user's package
            DB::table('users')
                ->where('email', $user_email)
                ->update(['package' => $package]);
            
                $this->dispatch(
                    'alert',
                    type: 'success',
                    title: 'Successful!',
                    text: "You've successfully updated the package of $user_email to ".ucfirst($package),
                    button: 'Got it!'
                );

                    //..............................
                    //....Send DB Notification + Sweet Alert
                    //..............................

            //Insert record into the transaction tb
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'package',
                'note' => $note,
                'operator_id' => auth()->id(),
                'status' => 'successful',
                'reference_number' => $ref_number
            ]);
            
            
                $this->form->fill();

        } else {
            // Handle the case where user is not found
            Notification::make()
            ->title('Invalid User action!')
            ->danger()
            ->send();
        }

    }else{
        return;
    }
    }
    

    public function render()
    {    
        return view('livewire.upgrade-n-downgrade');
    }
}
