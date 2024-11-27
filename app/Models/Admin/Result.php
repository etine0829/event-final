<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Result extends Model
{
    use HasFactory;

    protected $table = "result";

    protected $fillable = [
        'event_id',
        'category_id',
        'criteria_id',
        'participant_id',
        'user_id',
        'deduction',
        'total', 
        'rank',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

