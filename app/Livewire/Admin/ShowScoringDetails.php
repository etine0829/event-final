<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Category;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Scorecard;
use Illuminate\Support\Facades\Auth;

class ShowScoringDetails extends Component
{
    public $category;
    public $participants = [];
    public $criteria = [];
    public $genderFilter = 'all'; // Default filter
    public $scores = [];
    public $isValidated = false;

    public function mount($categoryId)
    {
        $this->category = Category::findOrFail($categoryId);
        $this->loadParticipants();
        $this->loadCriteria();
        $this->loadScores();
    }

    private function loadParticipants()
    {
        $this->participants = Participant::where('event_id', $this->category->event_id)
                                         ->with(['group', 'event'])
                                         ->get();
    }

    private function loadCriteria()
    {
        $this->criteria = Criteria::where('event_id', $this->category->event_id)
                                  ->where('category_id', $this->category->id)
                                  ->get();
    }

    private function loadScores()
    {
        // The key will be like: "participantId.criteriaId"
        list($participantId, $criteriaId) = explode('.', $key);

        // Update the score in the array
        $this->scores[$participantId][$criteriaId] = $value;

        session()->put('scores', $this->scores);

         // After updating, mark as not validated if errors exist
         $this->isValidated = false;  // Reset the validation state
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

    public function updatedScores($value, $key)
    {
        list($participantId, $criteriaId) = explode('.', $key);
        $this->scores[$participantId][$criteriaId] = $value;
    }

    public function updatedGenderFilter()
    {
        $this->loadParticipants();
        $this->loadScores();
    }

    public function saveScores()
    {   
        // Store the current scores in the session so that they persist after a page reload
        session()->put('scores', $this->scores);
        // First, run the validation
        $this->validateScores();
    
        // If validation fails, don't proceed and just show the validation errors
        if (session()->has('validationErrors')) {
           // Save the validation failure status to session
            session()->put('isValidated', false);
            $this->isValidated = false;  // Update the local variable
            return;  // Early return if validation fails
        }
    
        // If validation passes, proceed with saving scores
        $judgeId = Auth::id();
        $categoryScore = $this->category->score; // Base category score
    
        \Log::info('Category Score: ' . $categoryScore);
    
        foreach ($this->scores as $participantId => $criteriaScores) {
            $totalScore = 0;
    
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
            }
    
            // Calculate the average score using the formula
            $avgScore = ($totalScore * $categoryScore) / 100;
    
            // Ensure avg_score is formatted as a string
            $formattedAvgScore = number_format($avgScore, 2, '.', '');
    
            // Update the average score in the Scorecard table
            Scorecard::where('category_id', $this->category->id)
                    ->where('participant_id', $participantId)
                    ->where('user_id', $judgeId)
                    ->update(['avg_score' => $formattedAvgScore]);
        }
    
        // If no validation errors occurred, flash success message
        session()->flash('success', 'Scores saved and average scores updated successfully!');
        // Mark as validated after successful submission
        session()->put('isValidated', true);  // Save validation success to session
        $this->isValidated = true;  // Update local variable
        // Determine if this is a new submission or an update
        $isNewSubmission = !$this->isValidated; // If not validated before, it's a new submission
        if ($isNewSubmission) {
            session()->flash('success', 'Scores submitted successfully!');
        } else {
            session()->flash('success', 'Scores updated successfully!');
        }
    }
    
    private function validateScores()
    {
        $validationErrors = [];
    
        foreach ($this->scores as $participantId => $criteriaScores) {
            foreach ($criteriaScores as $criteriaId => $score) {
                $criterion = $this->criteria->firstWhere('id', $criteriaId);
    
                if (is_null($score) || $score === '') {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId cannot be empty.";
                } elseif ($score > $criterion->criteria_score) {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId exceeds the maximum of {$criterion->criteria_score}.";
                }   elseif ((int)$score === 0 || preg_match('/^0\d/', (string)$score)) {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId cannot be 0 or start with 0.";
                }
            }
        }
    
        if (!empty($validationErrors)) {
            // Store the validation errors to flash
            session()->flash('validationErrors', $validationErrors);
            session()->put('isValidated', false);  // Store validation failure to session
            $this->isValidated = false;
        }
    }
    
    {
        $validationErrors = $this->validateScores();

        if (!empty($validationErrors)) {
            session()->flash('error', implode(' ', $validationErrors));
            return;
        }

        $this->saveScorecardData();
        session()->flash('success', 'Scores saved and average scores updated successfully!');
    }

    private function validateScores()
    {
        $validationErrors = [];
        $scoringType = $this->category->event->type_of_scoring;

        if ($scoringType === 'points') {
            $validationErrors = $this->validatePointsScores();
        } elseif (in_array($scoringType, ['ranking(H-L)', 'ranking(L-H)'])) {
            $validationErrors = $this->validateRankingScores($scoringType);
        } else {
            $validationErrors[] = "Invalid scoring type.";
        }

        return $validationErrors;
    }

    private function validatePointsScores()
    {
        $validationErrors = [];

        foreach ($this->scores as $participantId => $criteriaScores) {
            foreach ($criteriaScores as $criteriaId => $score) {
                $criterion = $this->criteria->firstWhere('id', $criteriaId);

                if (is_null($score) || $score === '') {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId must not be empty.";
                } elseif ($score > $criterion->criteria_score) {
                    $validationErrors[] = "Score for Participant ID $participantId and Criteria ID $criteriaId exceeds the maximum of {$criterion->criteria_score}.";
                }
            }
        }

        return $validationErrors;
    }

    private function validateRankingScores($scoringType)
    {
        $validationErrors = [];
        $numParticipants = $this->participants->count();

        foreach ($this->scores as $participantId => $criteriaScores) {
            foreach ($criteriaScores as $criteriaId => $rank) {
                if (is_null($rank) || $rank === '') {
                    $validationErrors[] = "Rank for Participant ID $participantId and Criteria ID $criteriaId must not be empty.";
                } elseif ($rank < 1 || $rank > $numParticipants) {
                    $validationErrors[] = "Rank for Participant ID $participantId and Criteria ID $criteriaId must be between 1 and $numParticipants for ranking.";
                }
            }
        }

        if (in_array($scoringType, ['ranking(H-L)', 'ranking(L-H)'])) {
            $validationErrors = array_merge($validationErrors, $this->validateUniqueRanks());
        }

        if ($scoringType === 'ranking(L-H)') {
            $validationErrors = array_merge($validationErrors, $this->validateLowToHighRanking());
        }

        return $validationErrors;
    }

    private function validateUniqueRanks()
    {
        $validationErrors = [];

        foreach ($this->criteria as $criterion) {
            $ranks = [];
            foreach ($this->scores as $participantId => $criteriaScores) {
                if (isset($criteriaScores[$criterion->id])) {
                    $ranks[] = $criteriaScores[$criterion->id];
                }
            }

            if (count($ranks) !== count(array_unique($ranks))) {
                $validationErrors[] = "Ranks for criterion ID {$criterion->id} must be unique (no duplicates).";
            }
        }

        return $validationErrors;
    }

    private function validateLowToHighRanking()
    {
        $validationErrors = [];

        foreach ($this->criteria as $criterion) {
            $ranks = [];
            foreach ($this->scores as $participantId => $criteriaScores) {
                if (isset($criteriaScores[$criterion->id])) {
                    $ranks[] = $criteriaScores[$criterion->id];
                }
            }

            $sortedRanks = collect($ranks)->sort()->values()->all();

        }

        return $validationErrors;
    }

    private function saveScorecardData()
{
    $judgeId = Auth::id();
    $categoryScore = $this->category->score; // Category-specific score weight
    $eventId = $this->category->event_id;
    $scoringType = $this->category->event->type_of_scoring;

    foreach ($this->scores as $participantId => $criteriaScores) {
        $totalScore = 0;
        $criteriaCount = count($criteriaScores); // Number of criteria for average calculation

        foreach ($criteriaScores as $criteriaId => $score) {
            // Update or create scorecard entry for the participant and criterion
            Scorecard::updateOrCreate(
                [
                    'category_id' => $this->category->id,
                    'participant_id' => $participantId,
                    'criteria_id' => $criteriaId,
                    'user_id' => $judgeId,
                    'event_id' => $eventId,
                ],
                ['score' => $score]
            );

            $totalScore += $score; // Add the score for each criterion to total score
        }

        if ($scoringType === 'points') {
            // Calculate average score for points-based scoring (using category weight)
            $avgScore = ($totalScore * $categoryScore) / 100;
        } elseif (in_array($scoringType, ['ranking(H-L)', 'ranking(L-H)'])) {
            // Calculate average score for ranking-based scoring (sum of scores divided by number of criteria)
            $avgScore = $totalScore / $criteriaCount;
        } else {
            $avgScore = 0; // Default fallback
        }

        // Format the average score to two decimal places
        $formattedAvgScore = number_format($avgScore, 2, '.', '');

        // Update the average score for the participant
        Scorecard::where('category_id', $this->category->id)
                 ->where('participant_id', $participantId)
                 ->where('user_id', $judgeId)
                 ->update(['avg_score' => $formattedAvgScore]);
    }
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
