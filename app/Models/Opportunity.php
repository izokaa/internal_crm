<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    use HasFactory;

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
}
