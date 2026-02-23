<?php

namespace App\Filament\Resources\PmuResource\Pages;

use App\Filament\Resources\PmuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPmu extends EditRecord
{
    protected static string $resource = PmuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
