<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Judge extends Model
{
    protected $fillable = ['name', 'email', 'event_id'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

}