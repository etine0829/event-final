<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $table = "result";

    protected $fillable = [
        'category_id',
        'criteria_id',
        'participant_id',
        'user_id',
        'score',
        'avg_score', 
    ];
}
