<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function getSettingsAttribute($value)
    {   
        return json_decode($value);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clarification()
    {
        return $this->hasOne(Clarification::class);
    }
}
