<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'date_contrat' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class);
    }

}
