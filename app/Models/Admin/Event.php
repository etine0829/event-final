<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use \App\Models\Admin\Event; 

class Event extends Model
{
    use HasFactory, HasRoles;

    protected $table = "events";

    protected $fillable =[
        'event_name',
        // 'event_date',
        'venue',
        'type_of_scoring',
    ];

    // public function category(): HasMany{
    //     return $this->hasMany(Category::class);
    // }
    
}
