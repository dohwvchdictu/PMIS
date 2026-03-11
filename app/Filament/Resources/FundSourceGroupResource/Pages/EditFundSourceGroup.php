<?php

namespace App\Filament\Resources\FundSourceGroupResource\Pages;

use App\Filament\Resources\FundSourceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFundSourceGroup extends EditRecord
{
    protected static string $resource = FundSourceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
