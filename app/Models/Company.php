<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Offer;
use App\Models\User;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    public function offers()
    {
        return $this->hasMany(Offer::class, 'company_id');
    }

    public function User()
    {
        return $this->hasOne(User::class,'company_id') ;
    }

    protected $fillable =  [
        'name',
    ];
}
