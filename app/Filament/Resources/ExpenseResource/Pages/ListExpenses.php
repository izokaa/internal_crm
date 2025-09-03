<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ExpenseResource\Widgets\ExpenseWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\ExpenseCategory;
use App\Models\Contact;
use App\Models\Expense;
use App\Enums\ModePayment;
use App\Enums\ExpenseStatus;
use Carbon\Carbon;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;


    public static function getWidgets(): array
    {
        return [
            ExpenseWidget::class
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseWidget::class
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->processCollectionUsing(function (string $modelClass, Collection $rows) {
                    foreach ($rows as $row) {
                        // Normaliser les clés (minuscules + trim sans accents)
                        $data = collect($row)->mapWithKeys(function ($value, $key) {
                            $normalizedKey = strtolower(trim($key));
                            $normalizedKey = str_replace([' ', '-', 'é'], ['_', '_', 'e'], $normalizedKey);
                            return [$normalizedKey => $value];
                        });


                        // Récupération des champs Excel
                        $montantHt       = $data->get('montant_ht');
                        $montantTtc      = $data->get('montant_ttc');
                        $devise          = $data->get('devise');
                        $clientEmail = $data->get('email_du_client');
                        $supplierEmail = $data->get('email_du_fournisseur');
                        $tva             = $data->get('tva');
                        $dateExpense             = $data->get('date_expense');
                        $category          = $data->get('categorie');
                        $statut          = $data->get('status');
                        $modePayment          = $data->get('mode_de_paiement');

                        // Relations

                        $client = $clientEmail ? Contact::firstOrCreate(['email' => $clientEmail]) : null;
                        $supplier = $supplierEmail ? Contact::firstOrCreate(['email' => $supplierEmail]) : null;
                        $expenseCategory = $category ? ExpenseCategory::firstOrCreate(['nom' => $category]) : null;

                        Log::info($data);

                        // Création du contrat
                        Expense::create([
                            'date_expense'     => $dateExpense ? Carbon::parse($dateExpense) : null,
                            'montant_ht'       => $montantHt,
                            'montant_ttc'      => $montantTtc,
                            'devise'           => $devise,
                            'tva'              => $tva,
                            'supplier_id' => $supplier->id,
                            'client_id' => $client->id,
                            'category_id' => $expenseCategory->id,
                            'status'  =>  collect(ExpenseStatus::cases())->first(fn ($case) => $case->getLabel() === $statut)?->value,
                            'mode_payment'  =>  collect(ModePayment::cases())->first(fn ($case) => $case->getLabel() === $modePayment)?->value,
                        ]);
                    }

                    return $rows;
                })

        ];
    }
}
