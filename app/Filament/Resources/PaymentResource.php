<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-s-currency-euro';

    protected static ?string $navigationLabel = 'Pagos';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Usuario')->searchable(),
                TextColumn::make('user.profession.salary')->label('Salario'),
                TextColumn::make('bonus')->label('Bonus'),
                TextColumn::make('discount')->label('Descuento'),
                TextColumn::make('salary')->label('Salario Total'),
                TextColumn::make('description')->label('Descripción')->limit(50),
                IconColumn::make('state')->label('Estado')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-s-arrow-path-rounded-square',
                        '1' => 'heroicon-o-x-circle',
                        '2' => 'heroicon-m-check',
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Editar')->icon('heroicon-m-pencil-square')->iconButton()
                ->fillForm(fn (Payment $payment): array => [
                    'idUser' => $payment->idUser,
                    'salary' => $payment->salary,
                    'discount' => $payment->discount,
                    'bonus' => $payment->bonus,
                    'description' => $payment->description,
                    'state' => $payment->state,
                ])
                ->form([
                    Section::make('Datos del usuario')
                    ->description('No abuses del limite de intentos.')
                    ->schema([
                        Select::make('idUser')
                            ->relationship(name: 'user', titleAttribute: 'dni')
                            ->searchable()
                            ->required(),
                        TextInput::make('bonus')->label('Bonus')->required()->string(),
                        TextInput::make('discount')->label('Descuento')->required()->numeric(),
                        TextInput::make('salary')->label('Salario Total')->required(),
                        Textarea::make('description')->label('Descripción')->required()->minLength(20)->columnSpanFull(),
                    ])->columns(4),
                ])
                ->action(function (array $data, Payment $payment): void {
                    $payment->idUser = $data['idUser'];
                    $payment->salary = $data['salary'];
                    $payment->discount = $data['discount'];
                    $payment->bonus = $data['bonus'];
                    $payment->description = $data['description'];
                    $payment->state = $data['state'];
                    $payment->save();
                }),
                Action::make('Marcar')->icon('heroicon-m-list-bullet')->iconButton()
                ->fillForm(fn (Payment $payment): array => [
                    'state' => $payment->state,
                ])
                ->form([
                    Section::make('Datos del usuario')
                    ->description('No abuses del limite de intentos.')
                    ->schema([
                        Select::make('state')
                            ->options([
                                '0' => 'No Pagado',
                                '1' => 'Anulado',
                                '2' => 'Pagado',
                            ]),
                    ]),
                ])
                ->action(function (array $data, Payment $payment): void {
                    $payment->state = $data['state'];
                    $payment->save();
                }),
                Action::make('pdf')->icon('heroicon-m-inbox-arrow-down')->iconButton()
                ->url(fn (Payment $payment): string => route('payments.pdf', ['payment' => $payment, 'user' => $payment->idUser]))
                ->openUrlInNewTab()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                Action::make('generate')
                ->label('Generar Pagos')
                ->url(fn (): string => route('payments.generate')),
                Action::make('delete')
                ->label('Eliminar Pagos')
                ->requiresConfirmation()
                ->url(fn (): string => route('payments.delete'))->color(Color::Red),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
