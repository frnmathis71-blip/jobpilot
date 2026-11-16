<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PARENT_MODELE;

class MODELE extends Model
{
    use HasFactory;

    protected $table = 'PLURIEL';

    protected $fillable = [
        'CHAMP1',
        'CHAMP2',
        'PARENT_SINGULIER_id',
    ];

    // Un SINGULIER appartient à un PARENT_SINGULIER
    public function PARENT_SINGULIER()
    {
        return $this->belongsTo(PARENT_MODELE::class, 'PARENT_SINGULIER_id');
    }
}
