<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MODELE;

class PARENT_MODELE extends Model
{
    use HasFactory;

    protected $table = 'PARENT_PLURIEL';

    protected $fillable = [
        'PARENT_CHAMP',
    ];

    // Un PARENT_SINGULIER possède plusieurs PLURIEL
    public function PLURIEL()
    {
        return $this->hasMany(MODELE::class, 'PARENT_SINGULIER_id');
    }
}
