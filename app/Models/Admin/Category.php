<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\Event; 

class Category extends Model
{
    use HasFactory;
    protected $table = "category";

    protected $fillable = [
        'event_id',
        'category_id',
        'category_name',
        'score',
            
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

