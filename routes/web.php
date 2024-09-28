<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CriteriaController;


Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // Route::get('/manage-event', [EventController::class, 'index'])->name('event.dashboard');

        Route::resource('event', EventController::class)->names([
            'index' => 'event.index',
            'create' => 'event.create',
            'store' => 'event.store',
            'edit' => 'event.edit',
            'update' => 'event.update'
        ]);
        Route::delete('event', [EventController::class, 'deleteAll'])->name('EventController.deleteAll');

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
    }); 
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
