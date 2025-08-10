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
        'for_call',
    ];

    protected $casts = [
        'for_task' => 'boolean',
        'for_event' => 'boolean',
        'for_call' => 'boolean',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public static function taskLabels()
    {
        return self::where('for_task', true)->get();
    }

    public static function eventLabels()
    {
        return self::where('for_event', true)->get();
    }

    public static function callLabels()
    {
        return self::where('for_call', true)->get();
    }


}
