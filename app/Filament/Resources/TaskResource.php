<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-s-pencil';

    protected static ?string $navigationLabel = 'Tareas';

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
                    TextInput::make('name')->label('Tarea')->required(),
                    Select::make('state')
                        ->options([
                            '1' => 'Compleado',
                            '2' => 'Atrasado',
                            '3' => 'No completado',
                        ]),
                    Textarea::make('description')->label('Descripción')->required()->columnSpanFull(),
                ])->columns(3)->hidden(!auth()->user()->isAdmin()),
                Section::make('Plazo de las tareas')
                ->description('No abuses del limite de intentos.')
                ->schema([
                    DateTimePicker::make('dateStart')->label('Fecha de inicio')->required(),
                    DateTimePicker::make('dateEnd')->label('Fecha de finalización')->required(),
                ])->columns(2)->hidden(!auth()->user()->isAdmin()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('name')->label('Tarea')->searchable(),
                TextColumn::make('description')->label('Descripción')->limit(50),
                // TextColumn::make('state')->label('Estado'),
                IconColumn::make('state')
                    ->icon(fn (string $state): string => match ($state) {
                        '1' => 'heroicon-s-check-circle',
                        '2' => 'heroicon-s-x-circle',
                        '3' => 'heroicon-m-ellipsis-horizontal',
                    }),
                TextColumn::make('dateStart')->label('Fecha de inicio')->dateTime(),
                TextColumn::make('dateEnd')->label('Fecha de finalizacion')->dateTime(),
            ])
            ->filters([
                Filter::make('dateStart')
                    ->form([
                        DateTimePicker::make('created_from')->label('Creado desde'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->where('dateStart', '>=', $date),
                            );
                    }),
                Filter::make('dateEnd')
                    ->form([
                        DateTimePicker::make('created_until')->label('Creado hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->where('dateEnd', '<=', $date),
                            );
                    }),
                SelectFilter::make('state')->label('Estado')
                    ->multiple()
                    ->options([
                        '1' => 'Completado',
                        '2' => 'Atrasado',
                        '3' => 'No completado',
                    ])
            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
                ->fillForm(fn (Task $task): array => [
                    'idUser' => $task->idUser,
                    'name' => $task->name,
                    'description' => $task->description,
                    'state' => $task->state,
                    'dateStart' => $task->dateStart,
                    'dateEnd' => $task->dateEnd,
                ])
                ->form([
                    Section::make('Datos del usuario')
                    ->description('No abuses del limite de intentos.')
                    ->schema([
                        Select::make('idUser')->label('Usuario')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')->label('Tarea')->required(),
                        Select::make('state')
                            ->options([
                                '1' => 'Compleado',
                                '2' => 'Atrasado',
                                '3' => 'No completado',
                            ]),
                        Textarea::make('description')->label('Descripción')->required()->columnSpanFull(),
                    ])->columns(3),
                    Section::make('Plazo de las tareas')
                    ->description('No abuses del limite de intentos.')
                    ->schema([
                        DateTimePicker::make('dateStart')->label('Fecha de inicio')->required(),
                        DateTimePicker::make('dateEnd')->label('Fecha de finalización')->required(),
                    ])->columns(2),
                ])
                ->action(function (array $data, Task $task): void {
                    $task->idUser = $data['idUser'];
                    $task->name = $data['name'];
                    $task->description = $data['description'];
                    $task->state = $data['state'];
                    $task->dateStart = $data['dateStart'];
                    $task->dateEnd = $data['dateEnd'];
                    $task->save();
                })->hidden(!auth()->user()->isAdmin()),
            Tables\Actions\DeleteAction::make()->label('Eliminar')->icon('heroicon-m-trash')->iconButton()
                ->modalHeading('Eliminar tarea')
                ->modalDescription('¿Estás seguro de eliminar esta tarea?. Está acción no puede deshacerse.')
                ->modalSubmitActionLabel('Sí, ¡Elimínalo!')
                ->modalCancelActionLabel('Cancelar')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Tarea eliminada.')
                        ->body('Esta tarea ha sido eliminado exitosamente.')
                )->hidden(!auth()->user()->isAdmin()),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
