<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\FactureStatus;

class Facture extends Model
{
    protected $fillable = [
        'numero_facture',
        'date_facture',
        'echeance_payment',
        'montant_ht',
        'montant_ttc',
        'tva',
        'status',
        'contrat_id',
        'devise'
    ];

    protected $casts = [
        'status' => FactureStatus::class,
        'date_facture' => 'date',
        'echeance_payment' => 'date'
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }


    protected static function booted()
    {
        static::creating(function ($facture) {
            $lastId = self::max('id') + 1;
            if (!$facture->numero_facture || empty($facture->numero_facture)) {
                $facture->numero_facture = 'FACTURE-' . date('Y') . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
            }
        });
    }


    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class);
    }



}
