<?php

use App\Http\Controllers\Api\QrLookupController;
use Illuminate\Support\Facades\Route;

Route::get('/qr-lookup', [QrLookupController::class, 'lookup']);
