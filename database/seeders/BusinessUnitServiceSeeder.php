<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessUnit;
use App\Models\Service;

class BusinessUnitServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buVentes = BusinessUnit::create(['nom' => 'Ventes']);
        $buSupport = BusinessUnit::create(['nom' => 'Support Client']);
        $buMarketing = BusinessUnit::create(['nom' => 'Marketing']);
        $buRH = BusinessUnit::create(['nom' => 'Ressources Humaines']);

        $slutionsEtLogiciels = BusinessUnit::create(['nom' => 'Solutions & Logiciels']);
        $corporateServices = BusinessUnit::create(['nom' => 'Corporate Services']);
        $dataMarket = BusinessUnit::create(['nom' => 'Data & Market']);

        Service::create(['nom' => 'Prospection Commerciale', 'business_unit_id' => $buVentes->id]);
        Service::create(['nom' => 'Fidélisation Client', 'business_unit_id' => $buVentes->id]);
        Service::create(['nom' => 'Gestion des Comptes', 'business_unit_id' => $buVentes->id]);

        Service::create(['nom' => 'Assistance Technique', 'business_unit_id' => $buSupport->id]);
        Service::create(['nom' => 'Support Produit', 'business_unit_id' => $buSupport->id]);

        Service::create(['nom' => 'Campagnes Digitales', 'business_unit_id' => $buMarketing->id]);
        Service::create(['nom' => 'Relations Publiques', 'business_unit_id' => $buMarketing->id]);

        Service::create(['nom' => 'Recrutement', 'business_unit_id' => $buRH->id]);
        Service::create(['nom' => 'Paie', 'business_unit_id' => $buRH->id]);
    }
}
