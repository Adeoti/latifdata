<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Announcement;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = "Messaging";
    protected static ?int $navigationSort = 2;
    protected static bool $canCreateAnother = false; 

    // public static function canCreate(): bool
    //    {
    //    return Announcement::count() > 0? false : true;
         
    //    }

    // public static function canDelete(Model $record): bool
    // {
    //     return false;
    // }

    public static function form(Form $form): Form
    {

       

        $userId = auth()->id();
        return $form
            ->schema([

                Section::make('Announcement Message')->schema([
                    RichEditor::make('message')->label('')
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                    ->required()
                    ,
                    


                    Section::make('')->schema([
                       
                    Forms\Components\Hidden::make('user_id')->default($userId),
                    Select::make('style')->options([
                        'pop' => 'Pop',
                        'scroll' => 'Scroll',
                        'banner' => 'Banner'
                    ])
                    ->required()
                    ->native(false)
                    ])
                ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                
                TextColumn::make('message')
                ->words(4)
                ->markdown()
                ,
                TextColumn::make('style'),
                IconColumn::make('is_active')
                ->label('Active Status')
                ->boolean()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                Action::make('Toggle')
                    ->color('primary')
                    ->action(function(Model $record){
                        DB::table('announcements')
                        ->where('id', $record->id)
                        ->update([
                            'is_active' => $record->is_active ? false : true,
                        ]);
                        DB::table('announcements')
                        ->whereNot('id', $record->id)
                        ->update([
                            'is_active' => false,
                        ]);
                    })
                ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListAnnouncements::route('/'),
            //'create' => Pages\CreateAnnouncement::route('/create'),
            //'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
           
        ];
    }
}
