<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class ChangeUserPassword extends Component implements HasForms
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
                    TextInput::make('newPassword')
                        ->password()
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required()
                        ->revealable()
                        ->columnSpan(1),
                    Hidden::make('user_id')->default(Auth()->id()),
                   
                ])->columns(2)

            ])
            ->statePath('data');
    }



    public function changeUserPassword(): void
    {


        //Check if this user is actually a staff!

            $me = DB::table('users')->where('id',auth()->id())->first();

            if($me){

        $user_email = $this->form->getState()['email'];
        $newPassword = $this->form->getState()['newPassword'];

        $user = DB::table('users')->where('email', $user_email)->first();
    
        if ($user) {
            // Update the user's package
            DB::table('users')
                ->where('email', $user_email)
                ->update(['password' => $newPassword]);
            
                $this->dispatch(
                    'alert',
                    type: 'success',
                    title: 'Successful!',
                    text: "Password changed successfully!",
                    button: 'Great!'
                );
                    //..............................
                    //....Send DB Notification + Sweet Alert
                    //..............................

            
                $this->form->fill();

        } else {
            // Handle the case where user is not found
            $this->dispatch(
                'alert',
                type: 'error',
                title: 'Invalid User!',
                text: "Invalid User action",
                button: 'Got it!'
            );
            $this->form->fill();
            return;
        }


            }else{
                return;
            }

        
    }



    public function render()
    {
        return view('livewire.change-user-password');
    }
}
