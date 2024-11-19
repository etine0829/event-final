<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Spatie\Permission\Traits\HasRoles;
use App\Models\User;
use App\Models\Admin\Category;
use App\Models\Admin\Participant;


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

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function criteria()
    {
        return $this->hasMany(Criteria::class);
    }

    public function participant()
    {
        return $this->hasMany(Participant::class);
    }
    
    public function judges()
    {
        return $this->hasMany(Judge::class);
    }
    
    
}
