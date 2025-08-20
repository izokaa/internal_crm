<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOpportunity extends CreateRecord
{
    protected static string $resource = OpportunityResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        $opportunityData = collect($data)->except('piecesJointes')->toArray();

        $opportunity = static::getModel()::create($opportunityData);

        if (isset($data['piecesJointes'])) {
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $opportunity->piecesJointes()->create($pieceJointeData);
            }
        }

        return $opportunity;
    }

}
