<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'type',
        'pays_id',
        'specialite_id',
    ];

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }
}