<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;

// Public White-Label & Authentication Routes
Route::get('/tenant/resolve', [TenantController::class, 'resolve']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/invite/accept', [AuthController::class, 'acceptInvite']);

// Protected API Routes (Laravel Sanctum Secured)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/tenant/branding', [TenantController::class, 'updateBranding']);

    // Admin Panel Routes
    Route::get('/admin/employees', [AdminController::class, 'getEmployees']);
    Route::post('/admin/journey', [AdminController::class, 'saveJourney']);

    // Employee Panel Routes
    Route::get('/employee/modules', [EmployeeController::class, 'getModules']);
    Route::post('/employee/task/{id}/complete', [EmployeeController::class, 'completeTask']);
});

