<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PieceJointe extends Model
{
    use HasFactory;

    protected $fillable = ['contrat_id', 'nom_fichier', 'chemin_fichier'];

    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (PieceJointe $pieceJointe) {
            if ($pieceJointe->chemin_fichier) {
                Storage::disk('public')->delete($pieceJointe->chemin_fichier);
            }
        });

        static::updating(function (PieceJointe $pieceJointe) {
            if ($pieceJointe->isDirty('chemin_fichier')) {
                $originalPath = $pieceJointe->getOriginal('chemin_fichier');
                if ($originalPath) {
                    Storage::disk('public')->delete($originalPath);
                }
            }
        });
    }
}