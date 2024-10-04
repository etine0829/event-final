<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\Event; 

class Participant extends Model
{
    use HasFactory;
    protected $table = "participant";

    protected $fillable = [
        'event_id',
        'participant_photo',
        'participant_name',
        'participant_gender',
        'participant_comment',
        'participant_department'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
