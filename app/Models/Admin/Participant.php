<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\Event; 
use \App\Models\Admin\Group; 
use \App\Models\Admin\Scorecard; 
use \App\Models\Admin\Participant; 

class Participant extends Model
{
    use HasFactory;
    protected $table = "participant";

    protected $fillable = [
        'event_id',
        'group_id',
        'participant_photo',
        'participant_name',
        'participant_gender',
        'participant_comment',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function scorecards()
    {
        return $this->hasMany(Scorecard::class);
    }
}
