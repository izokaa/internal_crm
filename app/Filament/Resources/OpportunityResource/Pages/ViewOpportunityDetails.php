<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;

class ViewOpportunityDetails extends ViewRecord
{
    protected static string $resource = OpportunityResource::class;

    protected static string $view = 'filament.resources.opportunity-resource.pages.view-opportunity-details';

    public function infolist(Infolist $infolist): Infolist
    {
        return OpportunityResource::infolist($infolist);
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }
}
