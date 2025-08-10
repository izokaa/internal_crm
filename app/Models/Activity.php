<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ActivityStatut;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Activity extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                ->logOnly(['titre', 'statut']);
    }

    protected $fillable = [
        'titre',
        'description',
        'type',
        'statut', // WARNING: event, task or call 'type',
        'date_debut',
        'date_fin',
        'due_date',
        'prioritaire',
        'opportunity_id',
        'user_id',
        'is_all_day',
        'label_id',
        'contact_id'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'due_date' => 'datetime',
        'prioritaire' => 'boolean',
        'is_all_day' => 'boolean',
        'statut' => ActivityStatut::class
    ];

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

}
