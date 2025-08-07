<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtapePipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'ordre',
        'pipeline_id',
    ];

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }
}