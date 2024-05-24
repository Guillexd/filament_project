<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $navigationLabel = 'Usuarios';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos personales')
                    ->description('No abuses del limite de intentos.')
                    ->schema([
                        TextInput::make('name')->label('Nombre')->required()->string(),
                        TextInput::make('dni')->label('dni')->required()->numeric()->unique(ignoreRecord: true),
                        TextInput::make('email')->label('correo electrónico')->required()->string()->email()->unique(ignoreRecord: true),
                    ])->columns(3),
                Section::make('Datos laborales')
                    ->description('No abuses del limite de intentos.')
                    ->schema([
                        Select::make('idRole')->label('Rol')
                        ->relationship(name: 'role', titleAttribute: 'name')
                        ->required(),
                        Select::make('idProfession')->label('Profesión')
                        ->relationship(name: 'profession', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    ])->columns(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('Nombre'),
                TextColumn::make('dni')->sortable()->searchable()->label('dni'),
                TextColumn::make('email')->searchable()->label('correo electrónico'),
                TextColumn::make('profession.name')->label('Profesión'),
                TextColumn::make('role.name')->label('Rol')
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
                    ->fillForm(fn (User $user): array => [
                        'name' => $user->name,
                        'dni' => $user->dni,
                        'email' => $user->email,
                        'idRole' => $user->idRole,
                        'idProfession' => $user->idProfession,
                    ])
                    ->form([
                        Section::make('Datos personales')
                            ->description('No abuses del limite de intentos.')
                            ->schema([
                                TextInput::make('name')->label('Nombre')->required()->string(),
                                TextInput::make('dni')->label('dni')->required()->numeric()->unique(ignoreRecord: true),
                                TextInput::make('email')->label('correo electrónico')->required()->string()->email()->unique(ignoreRecord: true),
                            ])->columns(3),
                        Section::make('Datos laborales')
                            ->description('No abuses del limite de intentos.')
                            ->schema([
                                // TextInput::make('idRole')->label('Rol')->required(),
                                Select::make('idRole')
                                    ->relationship(name: 'role', titleAttribute: 'name')->required(),
                                Select::make('idProfession')
                                    ->relationship(name: 'profession', titleAttribute: 'name')
                                    ->searchable()
                                    ->required(),
                            ])->columns(2),
                    ])
                    ->action(function (array $data, User $user): void {
                        $user->name = $data['name'];
                        $user->dni = $data['dni'];
                        $user->email = $data['email'];
                        $user->idRole = $data['idRole'];
                        $user->idProfession = $data['idProfession'];
                        $user->save();
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
