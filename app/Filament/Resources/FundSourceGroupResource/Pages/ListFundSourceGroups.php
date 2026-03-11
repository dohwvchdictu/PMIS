<?php

namespace App\Filament\Resources\FundSourceGroupResource\Pages;

use App\Filament\Resources\FundSourceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFundSourceGroups extends ListRecords
{
    protected static string $resource = FundSourceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
