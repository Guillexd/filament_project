<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionResource\Pages;
use App\Filament\Resources\ProfessionResource\RelationManagers;
use App\Models\Profession;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfessionResource extends Resource
{
    protected static ?string $model = Profession::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationLabel = 'Profesiones';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        TextInput::make('name')->label('Nombre')->required()->string(),
                        TextInput::make('salary')->label('Salario')->required()->numeric(),
                        TextInput::make('bonus')->label('Bonus')->required()->numeric(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('Nombre'),
                TextColumn::make('salary')->sortable()->label('Salario'),
                TextColumn::make('bonus')->sortable()->label('Bonus'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
        ->fillForm(fn (Profession $profession): array => [
                    'name' => $profession->name,
                    'salary' => $profession->salary,
                    'bonus' => $profession->bonus,
                ])
                ->form([
                    Section::make('Datos de la profesión')
                        ->description('No abuses del limite de intentos.')
                        ->schema([
                            TextInput::make('name')->label('Nombre')->required()->string(),
                            TextInput::make('salary')->label('Salario')->required()->numeric(),
                            TextInput::make('bonus')->label('Bonus')->required()->numeric(),
                        ])->columns(3),
                ])
                ->action(function (array $data, Profession $profession): void {
                    $profession->name = $data['name'];
                    $profession->salary = $data['salary'];
                    $profession->bonus = $data['bonus'];
                    $profession->save();
                }),
            Tables\Actions\DeleteAction::make()->label('Eliminar')->icon('heroicon-m-trash')->iconButton()
                ->modalHeading('Eliminar usuario')
                ->modalDescription('¿Estás seguro de eliminar a este usuario?. Está acción no puede deshacerse.')
                ->modalSubmitActionLabel('Sí, ¡Elimínalo!')
                ->modalCancelActionLabel('Cancelar')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Usuario eliminado.')
                        ->body('Este usuario ha sido eliminado exitosamente.')
                ),

        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
            ])->label('Acciones'),
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
            'index' => Pages\ListProfessions::route('/'),
            'create' => Pages\CreateProfession::route('/create'),
            'edit' => Pages\EditProfession::route('/{record}/edit'),
        ];
    }
}
