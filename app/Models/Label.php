<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'couleur',
        'for_task',
        'for_event',
    ];

    protected $casts = [
        'for_task' => 'boolean',
        'for_event' => 'boolean',
    ];

    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }
}
