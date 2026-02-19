<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;

class OrderFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';
    protected static ?string $title = 'Файлы';
    protected static string $view = 'filament.resources.order-resource.relation-managers.order-files';

    protected function getViewData(): array
    {
        return [
            'audioFiles' => $this->getOwnerRecord()->audioFiles,
            'coverFiles' => $this->getOwnerRecord()->coverFiles,
        ];
    }
}
