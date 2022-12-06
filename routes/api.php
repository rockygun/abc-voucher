<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::apiResource('customers', CustomerController::class);
Route::get('customers/check-eligible/{id}', [CustomerController::class, 'checkEligible']);
Route::get('customers/validate-photo/{id}', [CustomerController::class, 'validatePhoto']);

