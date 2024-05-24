<?php

namespace App\Filament\Resources\LaborDayResource\Pages;

use App\Filament\Resources\LaborDayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaborDay extends EditRecord
{
    protected static string $resource = LaborDayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
