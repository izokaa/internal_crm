<?php

namespace App\Filament\Resources\ContratResource\Pages;

use App\Enums\ContratStatus;
use App\Filament\Resources\ContratResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EditContrat extends EditRecord
{
    protected static string $resource = ContratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        // Log::info("form status : " . $data['status'] . " Old status " . $this->record->status->value);

        // incrémenter le nombre renouvellement du contrat.
        if ($data['status'] == ContratStatus::RENEWED->value && $this->record->status->value != ContratStatus::RENEWED->value) {
            $this->record->renewable_count += 1;
            // Log::info("renewable count: " . $this->record->renewable_count);
        }


        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $contratData = collect($data)->except('piecesJointes')->toArray();
        $montantHt = $contratData['montant_ht'];
        $tva = $contratData['tva'];
        $contratData['montant_ttc'] = $montantHt * (1 + ($tva / 100));

        $record->update($contratData);

        if (isset($data['piecesJointes'])) {
            $record->piecesJointes()->delete();
            foreach ($data['piecesJointes'] as $pieceJointeData) {
                $record->piecesJointes()->create($pieceJointeData);
            }
        }


        return $record;
    }
}
