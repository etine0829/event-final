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
        'custom_label_1',
        'custom_value_1',
        'custom_label_2',
        'custom_value_2'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
