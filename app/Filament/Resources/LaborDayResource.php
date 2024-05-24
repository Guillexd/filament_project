<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaborDayResource\Pages;
use App\Filament\Resources\LaborDayResource\RelationManagers;
use App\Models\LaborDay;
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

class LaborDayResource extends Resource
{
    protected static ?string $model = LaborDay::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Días laborables';

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
                TextColumn::make('mounth')->label('Mes'),
                TextColumn::make('quantity')->label('Cantidad de días laborables')->alignCenter(true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
                ->fillForm(fn (LaborDay $laborday): array => [
                    'quantity' => $laborday->quantity,
                ])
                ->form([
                    Section::make('Cantidad de días laborables dentro del mes.')
                        ->description('No abuses del limite de intentos.')
                        ->schema([
                            TextInput::make('quantity')->label('días')->required()->numeric(),
                        ])->columns(1),
                ])
                ->action(function (array $data, LaborDay $laborday): void {
                    $laborday->quantity = $data['quantity'];
                    $laborday->save();
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
            'index' => Pages\ListLaborDays::route('/'),
            'create' => Pages\CreateLaborDay::route('/create'),
            'edit' => Pages\EditLaborDay::route('/{record}/edit'),
        ];
    }
}
