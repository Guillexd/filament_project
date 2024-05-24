<?php

namespace App\Filament\Resources\LaborDayResource\Pages;

use App\Filament\Resources\LaborDayResource;
use Error;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Throwable;

class CreateLaborDay extends CreateRecord
{
    protected static string $resource = LaborDayResource::class;
    protected static bool $canCreateAnother = false;

    public function create(bool $another = false): void
    {
        try {
            throw new Error();
        } catch (Throwable $exception) {
            back();
        }
    }
}
