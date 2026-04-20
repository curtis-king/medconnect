<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisInscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'montant',
        'detail',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
        ];
    }

    /**
     * Get the formatted montant.
     */
    public function getMontantFormattedAttribute(): string
    {
        return number_format($this->montant, 2, ',', ' ').' €';
    }
}
