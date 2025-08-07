<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];

    public function etapePipelines()
    {
        return $this->hasMany(EtapePipeline::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }
}