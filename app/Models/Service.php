<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'business_unit_id',
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}