<?php

namespace App\Filament\Resources\ClientWebsiteResource\Pages;

use App\Filament\Resources\ClientWebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientWebsite extends EditRecord
{
    protected static string $resource = ClientWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
