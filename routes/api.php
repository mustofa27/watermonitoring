<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TandonApiController;

Route::get('/tandons/{name}', [TandonApiController::class, 'show']);
