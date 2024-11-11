<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Scorecard;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Category;

class ShowScores extends Component
{
    public $categories = [];

    public function mount()
    {
        // Fetch all categories, with their criteria and related scorecard records
        $categories = Category::with(['criteria.scorecards.participant'])->get();

        // Structure data by category, criteria, and participant scores
        foreach ($categories as $category) {
            // Only add category if it has criteria
            if ($category->criteria->isEmpty()) {
                continue; // Skip category if no criteria exist
            }

            $categoryData = [
                'name' => $category->category_name,
                'criteria' => [],
            ];

            foreach ($category->criteria as $criteria) {
                // Skip criteria if no scorecards are found
                if ($criteria->scorecards->isEmpty()) {
                    continue;
                }
                
                $criteriaData = [
                    'name' => $criteria->criteria_name,
                    'participants' => [],
                ];

                // Iterate through scorecards and fetch participant data
                foreach ($criteria->scorecards as $scorecard) {
                    // Default participant name in case it's null
                    $participantName = $scorecard->participant->participant_name ?? 'Unknown';
                    
                    // Push participant score and details to the criteria data
                    $criteriaData['participants'][] = [
                        'name' => $participantName,
                        'score' => $scorecard->score,
                        'avg_score' => $scorecard->avg_score ?? 'N/A',  // Default if avg_score is not available
                    ];
                }

                // Only add criteria if it has participants
                if (!empty($criteriaData['participants'])) {
                    $categoryData['criteria'][] = $criteriaData;
                }
            }

            // Only add category if it has criteria
            if (!empty($categoryData['criteria'])) {
                $this->categories[] = $categoryData;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.show-scores', [
            'categories' => $this->categories,
        ])->layout('layouts.portal');
    }
}
