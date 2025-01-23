<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// todo: Pages Route

Route::get('/userLoginPage',[UserController::class,'userLoginPage']);