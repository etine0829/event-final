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
    public $scoresExist = false;
    public $group;

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

    $query = Group::with('event');
    
    // Fetch scores for the logged-in judge
    $loggedInJudgeId = Auth::id();

    $this->scoresExist = false;  // Initialize flag to check if scores exist

    foreach ($this->participants as $participant) {
        foreach ($this->criteria as $criterion) {
            $existingScore = Scorecard::where('category_id', $categoryId)
                ->where('participant_id', $participant->id)
                ->where('criteria_id', $criterion->id)
                ->where('user_id', $loggedInJudgeId) // Filter by judge
                ->first();

            // Check if any score exists for this participant and judge
            if ($existingScore) {
                $this->scores[$participant->id][$criterion->id] = $existingScore->score;
                $this->scoresExist = true;  // Set flag if at least one score exists
            } else {
                $this->scores[$participant->id][$criterion->id] = null;
            }
        }
    }
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

    // Array to hold scores with total scores for sorting
    $scoresWithTotal = [];

    foreach ($this->scores as $participantId => $criteriaScores) {
        $totalScore = 0;

        // Sum the individual criteria scores
        foreach ($criteriaScores as $criteriaId => $score) {
            // Save individual criteria scores
            Scorecard::updateOrCreate(
                [
                    'category_id' => $this->category->id,
                    'participant_id' => $participantId,
                    'criteria_id' => $criteriaId,
                    'user_id' => $judgeId,
                    'event_id' => $this->category->event_id,
                ],
                ['score' => $score]
            );

            // Accumulate the score for ranking
            $totalScore += $score;
        }

        // Calculate the avg_score for points-based scoring system
        $event = $this->category->event;  // Get event type
        if ($event->type_of_scoring === 'points') {
            // For points scoring, use the weighted average
            $avgScore = ($totalScore * $categoryScore) / 100;
        } else {
            // For ranking-based scoring, just use the total score
            $avgScore = $totalScore;
        }

        // Ensure avg_score is formatted as a string
        $formattedAvgScore = number_format($avgScore, 2, '.', '');

        // Update the average score in the Scorecard table
        Scorecard::where('category_id', $this->category->id)
                ->where('participant_id', $participantId)
                ->where('user_id', $judgeId)
                ->update(['avg_score' => $formattedAvgScore]);

        // Add the total score to the array for ranking
        $scoresWithTotal[] = [
            'participant_id' => $participantId,
            'total_score' => $totalScore
        ];
    }

    // Ranking participants based on total_score (no avg_score needed for ranking)
    $event = $this->category->event;  // Get event type

    if ($event->type_of_scoring === 'ranking(H-L)') {
        // Rank 1 as the highest total_score (descending order)
        usort($scoresWithTotal, function ($a, $b) {
            return $b['total_score'] <=> $a['total_score']; // Descending order
        });
    } elseif ($event->type_of_scoring === 'ranking(L-H)') {
        // Rank 1 as the lowest total_score (ascending order)
        usort($scoresWithTotal, function ($a, $b) {
            return $a['total_score'] <=> $b['total_score']; // Ascending order
        });
    }

    // Assign rank to each participant based on total_score
    $rank = 1;
    foreach ($scoresWithTotal as $scoreData) {
        $participantId = $scoreData['participant_id'];
        $totalScore = $scoreData['total_score'];

        // Update the rank for each participant in the Scorecard
        Scorecard::where('category_id', $this->category->id)
                ->where('participant_id', $participantId)
                ->where('user_id', $judgeId);

        // Increment rank for the next participant
        $rank++;
    }

    // If no validation errors occurred, flash success message
    session()->flash('success', 'Scores saved, average scores, and rankings updated successfully!');
    // Mark as validated after successful submission
    session()->put('isValidated', true);  // Save validation success to session
    $this->isValidated = true;  // Update local variable

    // Flash message based on whether it's a new submission or an update
    if ($this->scoresExist) {
        session()->flash('success', 'Scores updated successfully!');
    } else {
        session()->flash('success', 'Scores submitted successfully!');
    }
}

   
private function validateScores()
{
    $validationErrors = [];
    $rankingScores = []; // This will hold the used ranking values for each criterion
    $hasRankingError = false;

    foreach ($this->scores as $participantId => $criteriaScores) {
        // Retrieve participant's name
        $participant = $this->participants->firstWhere('id', $participantId);
        $participantName = $participant ? $participant->name : 'Unknown Participant';

        foreach ($criteriaScores as $criteriaId => $score) {
            // Retrieve criterion's name
            $criterion = $this->criteria->firstWhere('id', $criteriaId);
            $criteriaName = $criterion ? $criterion->name : 'Unknown Criterion';

            // General validations that apply to both ranking and points
            // Check for empty score
            if (is_null($score) || $score === '') {
                $validationErrors[] = "Score cannot be empty.";
            }
            // Check if the score starts with 0 or is 0
            elseif ((int)$score === 0 || preg_match('/^0\d/', (string)$score)) {
                $validationErrors[] = "Score cannot be 0 or start with 0.";
            }

            // Ensure that the score is not negative for point-based scoring
            if ($this->category->event->type_of_scoring === 'points' && $score < 0) {
                $validationErrors[] = "Score cannot be negative.";
            }

            // Points-based validation
            if ($this->category->event->type_of_scoring === 'points') {
                // Check for score exceeding the maximum allowed score
                if ($score > $criterion->criteria_score) {
                    $validationErrors[] = "Score exceeds the maximum of {$criterion->criteria_score}.";
                }
            }

            // Ranking-based validation
            elseif ($this->category->event->type_of_scoring === 'ranking(H-L)' || $this->category->event->type_of_scoring === 'ranking(L-H)') {
                // Ensure ranking is within the valid range
                $maxRank = $this->participants->count();
                if ($score < 1 || $score > $maxRank) {
                    $validationErrors[] = "Ranking must be between 1 and $maxRank.";
                }

                // Check for decimal scores (if it's ranking scoring)
                if (is_float($score) || preg_match('/\.\d+/', (string)$score)) {
                    $validationErrors[] = "Ranking cannot be a decimal.";
                }

                // Check for duplicate rankings for this criterion
                if (isset($rankingScores[$criteriaId])) {
                    if (in_array((int)$score, $rankingScores[$criteriaId])) {
                        $validationErrors[] = "Duplicate ranking score. Each ranking must be unique.";
                    } else {
                        // Add the ranking score to the list for this criterion
                        $rankingScores[$criteriaId][] = (int)$score;
                    }
                } else {
                    // Initialize the rankingScores array for this criterion if it doesn't exist
                    $rankingScores[$criteriaId] = [(int)$score];
                }
            }
        }
    }

    // If there are any validation errors, store them in the session
    if (!empty($validationErrors)) {
        session()->flash('validationErrors', $validationErrors);
        session()->put('isValidated', false);  // Store validation failure to session
        $this->isValidated = false;
    }

    // If there are ranking errors but we want to submit and record, proceed with saving the scores
    if ($hasRankingError) {
        // Mark as "submitted" even with ranking errors
        session()->flash('warning', 'Some ranking errors were found, but the scores have been submitted.');
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



