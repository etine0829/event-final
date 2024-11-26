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
    public $isValidated = false;

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
        //initializa scores if stored in session
        if (session()->has('scores')) {
            $this->scores = session('scores');
        }
        //this will check validation status exist in the session
        $this->isValidated = session()->has('isValidated') ? session('isValidated') : false;
    }

    public function updatedScores($value, $key)
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
    


    public function render()
    {
        return view('livewire.admin.show-scoring-details', [
            'category' => $this->category,
            'participants' => $this->participants,
            'criteria' => $this->criteria,
        ])->layout('layouts.portal');
    }
}
