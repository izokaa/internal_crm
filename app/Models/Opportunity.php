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
        'devise',
        'date_echeance',
        'probabilite',
        'status',
        'prefix',
        'contact_id',
        'source_id',
        'pipeline_id',
        'etape_pipeline_id',
        'sort_order',
    ];

    protected $casts = [
        'date_echeance' => 'date',
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

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function etapePipeline()
    {
        return $this->belongsTo(EtapePipeline::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
