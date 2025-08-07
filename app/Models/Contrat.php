<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = [
        'date_contrat',
        'date_debut',
        'date_fin',
        'periode_contrat',
        'periode_unite',
        'montant_ht',
        'montant_ttc',
        'tva',
        'client_id',
        'devise'
    ];

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

}
