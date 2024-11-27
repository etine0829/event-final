<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\Scorecard; 
use \App\Models\Admin\Participant;
use \App\Models\User; 

class Scorecard extends Model
{
    use HasFactory;

    protected $table = "scorecard";

    protected $fillable = [
            'event_id',
            'category_id',
            'criteria_id',
            'participant_id',
            'user_id',
            'score',
            'avg_score', 
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
        return $this->belongsTo(User::class); // Assuming each scorecard belongs to one user
    }
}