<?php

namespace App\Filament\Resources\ResidentialEstateResource\Pages;

use App\Filament\Resources\ResidentialEstateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageResidentialEstates extends ManageRecords
{
    protected static string $resource = ResidentialEstateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
