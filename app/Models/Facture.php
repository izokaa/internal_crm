<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'numero_faccture',
        'date_facture',
        'echeance_payment',
        'montant_ht',
        'montant_ttc',
        'tva',
        'status',
        'contrat_id'
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
            $contrat->numero_contrat = 'FACTURE-' . date('Y') . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        });
    }


    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class);
    }



}

