<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    public function users()
    {
        return $this->belongsTo(User::class, 'JobPosting_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'JobPosting_id');
    }
}
