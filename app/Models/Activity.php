<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'type',
        'statut',
        'date_debut',
        'date_fin',
        'due_date',
        'prioritaire',
        'opportunity_id',
        'user_id',
        'location',
        'is_all_day',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'due_date' => 'datetime',
        'prioritaire' => 'boolean',
        'is_all_day' => 'boolean',
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }
}
