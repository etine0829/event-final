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
        'comment',
    ];

    public function category()
    {
        return $this->hasmany(Category::class);
    }
    public function criteria()
    {
        return $this->hasmany(Criteria::class);
    }
    public function participant()
    {
        return $this->hasmany(Participant::class);
    }
}
