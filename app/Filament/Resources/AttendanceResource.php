<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Carbon\Carbon;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard';

    protected static ?string $navigationLabel = 'Asistencias';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canView(Model $record): bool
    {
        return true;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Section::make('Datos del usuario')
                ->description('No abuses del limite de intentos.')
                ->schema([
                    Select::make('idUser')->label('Usuario')
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Textarea::make('description')->label('Descripción')->required()
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('description')->label('Descripción'),
                TextColumn::make('created_at')->label('Fecha')->dateTime(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Creado desde'),
                        DatePicker::make('created_until')->label('Creado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    // ...
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Creado desde ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ;
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Creado hasta ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })->columns(2)
            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(1)
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
                ->fillForm(fn (Attendance $attendance): array => [
                    'user' => $attendance->userId,
                    'description' => $attendance->description,
                ])
                ->form([
                    Section::make('Datos de la asistencia')
                        ->description('No abuses del limite de intentos.')
                        ->schema([
                            Select::make('idUser')
                                ->relationship(name: 'user', titleAttribute: 'name')
                                ->searchable()
                                ->preload()->required()->columnSpanFull(),
                            Textarea::make('description')
                                ->label('Descripción')
                                ->required()->columnSpanFull(),
                        ]),
                ])
                ->action(function (array $data, Attendance $user): void {
                    $user->name = $data['name'];
                    $user->dni = $data['dni'];
                    $user->email = $data['email'];
                    $user->idRole = $data['idRole'];
                    $user->idProfession = $data['idProfession'];
                    $user->save();
                }),
            Tables\Actions\DeleteAction::make()->label('Eliminar')->icon('heroicon-m-trash')->iconButton()
                ->modalHeading('Eliminar asistencia')
                ->modalDescription('¿Estás seguro de eliminar esta asistencia?. Está acción no puede deshacerse.')
                ->modalSubmitActionLabel('Sí, ¡Elimínalo!')
                ->modalCancelActionLabel('Cancelar')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Asistencia eliminada.')
                        ->body('Esta asistencia ha sido eliminado exitosamente.')
                ),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
