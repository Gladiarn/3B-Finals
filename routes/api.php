<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;

// my endpoints for students
Route::prefix('students')->group(function () {
    // get all
    Route::get('/', [StudentController::class, 'index']);
    //  posting
    Route::post('/', [StudentController::class, 'store']);
    // specific id or student
    Route::get('/{id}', [StudentController::class, 'show']);
    // update specific student
    Route::patch('/{id}', [StudentController::class, 'update']);

    // endpoints for subjects included
    Route::prefix('/{student}/subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::get('/{subject}', [SubjectController::class, 'show']);
        Route::patch('/{subject}', [SubjectController::class, 'update']);
    });
});

