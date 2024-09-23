<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "category";

    protected $fillable = [
        'category_name',
        'percentage',
        'event_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
        public function criteria()
    {
        return $this->hasMany(Criteria::class);
    }
}

