<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Spatie\Permission\Traits\HasRoles;
use App\Models\Admin\Event;
use App\Models\Admin\Participant;

class Group extends Model
{
    use HasFactory, HasRoles;
    
    protected $table = "group";

    protected $fillable =[
        'event_id',
        'group_name',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function participant()
    {
        return $this->hasMany(Participant::class);
    }
}