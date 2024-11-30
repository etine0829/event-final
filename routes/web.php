<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\JudgesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\ScorecardController;;
use App\Livewire\Admin\ShowScores;
use App\Livewire\Admin\ShowScoringDetails;

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('assign-judge', [JudgesController::class, 'showAssignJudgeForm'])->name('assign-judge.form');
        Route::post('assign-judge', [JudgesController::class, 'assignJudgeToEvent'])->name('assign-judge');


        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Route::get('/manage-event', [EventController::class, 'index'])->name('event.dashboard');

        Route::resource('event', EventController::class)->names([
            'index' => 'event.index',
            'create' => 'event.create',
            'store' => 'event.store',
            'edit' => 'event.edit',
            'update' => 'event.update'
        ]);
        Route::delete('event', [EventController::class, 'deleteAll'])->name('event.deleteAll');

        //category routes
        Route::resource('category', CategoryController::class)->names([
            'index' => 'category.index',
            'create' => 'category.create',
            'store' => 'category.store',
            'edit' => 'category.edit',
            'update' => 'category.update'
        ]);
        Route::delete('category', [CategoryController::class, 'deleteAll'])->name('category.deleteAll');

        Route::resource('criteria', CriteriaController::class)->names([
            'index' => 'criteria.index',
            'create' => 'criteria.create',
            'store' => 'criteria.store',
            'edit' => 'criteria.edit',
            'update' => 'criteria.update'   
        ]);
        Route::delete('criteria', [CriteriaController::class, 'deleteAll'])->name('criteria.deleteAll');

        Route::resource('group', GroupController::class)->names([
            'index' => 'group.index',
            'create' => 'group.create',
            'store' => 'group.store',
            'edit' => 'group.edit',
            'update' => 'group.update'   
        ]);
        Route::delete('group', [GroupController::class, 'deleteAll'])->name('group.deleteAll');

        Route::resource('participant', ParticipantController::class)->names([
            'index' => 'participant.index',
            'create' => 'participant.create',
            'store' => 'participant.store',
            'edit' => 'participant.edit',
            'update' => 'participant.update'   
        ]);
        Route::delete('participant', [ParticipantController::class, 'deleteAll'])->name('participant.deleteAll');

        Route::resource('judge', JudgesController::class)->names([
            'index' => 'judge.index',
            'create' => 'judge.create',
            'store' => 'judge.store',
            'edit' => 'judge.edit',
            'update' => 'judge.update'   
        ]);
        Route::delete('judge', [JudgesController::class, 'deleteAll'])->name('judge.deleteAll');

        Route::resource('user', UserController::class)->names([
            'index' => 'user.index',
            'create' => 'user.create',
            'store' => 'user.store',
            'edit' => 'user.edit',
            'update' => 'user.update'   
        ]);
        Route::delete('user', [UserController::class, 'deleteAll'])->name('user.deleteAll');

        // Route to show the form for assigning a judge
        Route::get('assign-judge', [JudgesController::class, 'showAssignJudgeForm'])->name('assign-judge.form');

        // Route to handle the judge assignment
        Route::post('assign-judge', [JudgesController::class, 'assignJudgeToEvent'])->name('assign-judge');
        
        Route::resource('result', ResultController::class)->names([
            'index' => 'result.index',
            'create' => 'result.create',
            'store' => 'result.store',
            'edit' => 'result.edit',
            'update' => 'result.update'   
        ]);
    });

});



//Event_manager

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:event_manager'])->prefix('event_manager')->name('event_manager.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'indexManager'])->name('dashboard');
        // Route::get('/manage-event', [EventController::class, 'index'])->name('event.dashboard');

        Route::resource('event', EventController::class)->names([
            'index' => 'event.index',
            'create' => 'event.create',
            'store' => 'event.store',
            'edit' => 'event.edit',
            'update' => 'event.update'
        ]);
        Route::delete('event', [EventController::class, 'deleteAll'])->name('event.deleteAll');

        //category routes
        Route::resource('category', CategoryController::class)->names([
            'index' => 'category.index',
            'create' => 'category.create',
            'store' => 'category.store',
            'edit' => 'category.edit',
            'update' => 'category.update'
        ]);
        Route::delete('category', [CategoryController::class, 'deleteAll'])->name('category.deleteAll');

        Route::resource('criteria', CriteriaController::class)->names([
            'index' => 'criteria.index',
            'create' => 'criteria.create',
            'store' => 'criteria.store',
            'edit' => 'criteria.edit',
            'update' => 'criteria.update'   
        ]);
        Route::delete('criteria', [CriteriaController::class, 'deleteAll'])->name('criteria.deleteAll');

       
        Route::resource('group', GroupController::class)->names([
            'index' => 'group.index',
            'create' => 'group.create',
            'store' => 'group.store',
            'edit' => 'group.edit',
            'update' => 'group.update'   
        ]);
        Route::delete('group', [GroupController::class, 'deleteAll'])->name('group.deleteAll');

        Route::resource('participant', ParticipantController::class)->names([
            'index' => 'participant.index',
            'create' => 'participant.create',
            'store' => 'participant.store',
            'edit' => 'participant.edit',
            'update' => 'participant.update'   
        ]);
        Route::delete('participant', [ParticipantController::class, 'deleteAll'])->name('participant.deleteAll');

        Route::resource('judge', JudgesController::class)->names([
            'index' => 'judge.index',
            'create' => 'judge.create',
            'store' => 'judge.store',
            'edit' => 'judge.edit',
            'update' => 'judge.update'   
        ]);
        Route::delete('judge', [JudgesController::class, 'deleteAll'])->name('judge.deleteAll');

      
    });

});



// judge route

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:judge'])->prefix('judge')->name('judge.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'indexJudge'])->name('dashboard');
    });


    Route::post('/scorecard/store', [ScorecardController::class, 'store'])->name('score.store');
   

    // Route for updating scores
    Route::post('/scorecard/update', [ScorecardController::class, 'update'])->name('score.update');
    Route::get('/scores/{eventId}', ShowScores::class)->name('scores.show');

});

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:judge_chairman'])->prefix('judge_chairman')->name('judge_chairman.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'indexJudge'])->name('dashboard');
    });


    Route::post('/scorecard/store', [ScorecardController::class, 'store'])->name('score.store');
    Route::get('/showscores/{categoryId}', ShowScoringDetails::class)->name('category.details');

    // Route for updating scores
    Route::post('/scorecard/update', [ScorecardController::class, 'update'])->name('score.update');
    Route::get('/scores/{eventId}', ShowScores::class)->name('scores.show');

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
