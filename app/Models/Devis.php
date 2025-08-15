<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DevisStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devis extends Model
{
    protected $fillable = [
        'totlal_ht',
        'total_ttc',
        'total_tva',
        'devis',
        'date_devis',
        'contact_id',
        'quote_number',
        'remise',
        'status',
        'date_emission',
        'validity_duration',
        'note',
    ];


    protected $casts = [
        'date_devis' => 'date',
        'date_emission' => 'date',
        'validity_duration' => 'integer',
        'remise' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'tva' => 'decimal:2',
        'status' => DevisStatus::class,
    ];



    protected static function booted()
    {
        static::creating(function ($quote) {
            $lastId = self::max('id') + 1;
            $quote->quote_number = 'DEV-' . date('Y') . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        });
    }


    public function piecesJointes()
    {
        return $this->hasMany(PieceJointe::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }


}
