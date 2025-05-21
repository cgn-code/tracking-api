<?php

namespace App\Filament\Resources\ClientWebsiteResource\Pages;

use App\Filament\Resources\ClientWebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientWebsites extends ListRecords
{
    protected static string $resource = ClientWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
