<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-m-tag';

    protected static ?string $navigationLabel = 'Roles';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('description')->label('Descripción'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
                ->fillForm(fn (Role $role): array => [
                    'name' => $role->name,
                    'description' => $role->description,
                ])
                ->form([
                    Section::make('Datos del rol')
                        ->description('No abuses del limite de intentos.')
                        ->schema([
                            TextInput::make('name')->label('Rol')->required(),
                            TextInput::make('description')->label('Profesión')->required()
                        ])->columns(2),
                ])
                ->action(function (array $data, Role $role): void {
                    $role->name = $data['name'];
                    $role->description = $data['description'];
                    $role->save();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ])->paginated(false);
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
            'index' => Pages\ListRoles::route('/'),
            // 'create' => Pages\CreateRole::route('/create'),
            // 'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
