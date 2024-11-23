<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scorecard extends Model
{
    use HasFactory;

    protected $table = "scorecard";

    protected $fillable = [
        'category_id',
        'criteria_id',
        'participant_id',
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
}