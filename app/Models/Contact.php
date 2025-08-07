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
        'ville_id',
        'specialite_id',
        'business_unit_id',
        'service_id',
    ];

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}