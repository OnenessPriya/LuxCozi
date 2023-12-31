<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    public function users() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function areas() {
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }
}
