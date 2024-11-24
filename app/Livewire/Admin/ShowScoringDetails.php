<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Category;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Group;
use App\Models\Admin\Event;
use App\Models\Admin\Scorecard;
use Illuminate\Support\Facades\Auth;

class ShowScoringDetails extends Component
{
    public $category;
    public $participants = [];
    public $criteria = [];
    public $groups = [];
    public $events = [];
    public $genderFilter = 'all'; // Default filter
    public $scores = [];

    public function mount($categoryId)
    {
        $this->category = Category::findOrFail($categoryId);

        // Fetch participants
        $this->participants = Participant::where('event_id', $this->category->event_id)
                                         ->with(['group', 'event'])
                                         ->get();

        // Fetch criteria
        $this->criteria = Criteria::where('event_id', $this->category->event_id)
                                  ->where('category_id', $categoryId)
                                  ->get();

        // Fetch scores for the logged-in judge
        $loggedInJudgeId = Auth::id();

        foreach ($this->participants as $participant) {
            foreach ($this->criteria as $criterion) {
                $existingScore = Scorecard::where('category_id', $categoryId)
                                          ->where('participant_id', $participant->id)
                                          ->where('criteria_id', $criterion->id)
                                          ->where('user_id', $loggedInJudgeId) // Filter by judge
                                          ->first();

                // Populate the scores array
                $this->scores[$participant->id][$criterion->id] = $existingScore ? $existingScore->score : null;
            }
        }
    }

    public function updatedScores($value, $key)
    {
        // The key will be like: "participantId.criteriaId"
        list($participantId, $criteriaId) = explode('.', $key);

        $this->scores[$participantId][$criteriaId] = $value;
    }

    public function updatedGenderFilter()
    {
        // Apply the gender filter
        $this->participants = Participant::when($this->genderFilter !== 'all', function ($query) {
            $query->where('participant_gender', $this->genderFilter);
        })
        ->where('event_id', $this->category->event_id)
        ->with(['group', 'event'])
        ->get();

        // Reload scores to match the filtered participants
        $loggedInJudgeId = Auth::id();

        foreach ($this->participants as $participant) {
            foreach ($this->criteria as $criterion) {
                $existingScore = Scorecard::where('category_id', $this->category->id)
                                          ->where('participant_id', $participant->id)
                                          ->where('criteria_id', $criterion->id)
                                          ->where('user_id', $loggedInJudgeId) // Filter by judge
                                          ->first();

                $this->scores[$participant->id][$criterion->id] = $existingScore ? $existingScore->score : null;
            }
        }
    }

    public function saveScores()
    {
        $validationErrors = [];

        foreach ($this->scores as $participantId => $criteriaScores) {
            foreach ($criteriaScores as $criteriaId => $score) {
                $criterion = $this->criteria->firstWhere('id', $criteriaId);

                if (is_null($score) || $score === '') {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId cannot be empty.";
                } elseif ($score > $criterion->criteria_score) {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId exceeds the maximum of {$criterion->criteria_score}.";
                }
            }
        }

        if (!empty($validationErrors)) {
            session()->flash('error', implode(' ', $validationErrors));
            return;
        }

        $judgeId = Auth::id();

        // Save scores and calculate the average score
        foreach ($this->scores as $participantId => $criteriaScores) {
            $totalScore = 0;
            $criteriaCount = 0;

            foreach ($criteriaScores as $criteriaId => $score) {
                // Save individual criteria scores
                Scorecard::updateOrCreate(
                    [
                        'category_id' => $this->category->id,
                        'participant_id' => $participantId,
                        'criteria_id' => $criteriaId,
                        'user_id' => $judgeId,
                    ],
                    ['score' => $score]
                );

                $totalScore += $score;
                $criteriaCount++;
            }

            // Calculate and store the average score
            $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

            Scorecard::where('category_id', $this->category->id)
                     ->where('participant_id', $participantId)
                     ->where('user_id', $judgeId)
                     ->update(['avg_score' => $avgScore]);
        }

        session()->flash('success', 'Scores saved and average scores updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.show-scoring-details', [
            'category' => $this->category,
            'participants' => $this->participants,
            'criteria' => $this->criteria,
        ])->layout('layouts.portal');
    }
}
