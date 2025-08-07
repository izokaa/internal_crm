<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'note',
        'montant_estime',
        'date_echeance',
        'probabilite',
        'brief',
        'status',
        'prefix',
        'contact_id',
        'source_id',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}