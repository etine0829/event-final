<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\Event; 
use \App\Models\Admin\Category; 


class Criteria extends Model
{
    use HasFactory;
    protected $table = 'criteria';

    protected $fillable = [
        'event_id',
        'category_id',
        'criteria_name',
        'criteria_score',
    ];


    //each courses belongs to one event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Each courses belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scorecards()
    {
        return $this->hasMany(Scorecard::class);
    }
    
}