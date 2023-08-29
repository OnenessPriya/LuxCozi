<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function areas() {
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }

    public function distributors() {
        return $this->belongsTo('App\Models\User', 'distributor_id', 'id');
    }
}
