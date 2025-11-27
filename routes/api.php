<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// --- Public Routes (Bisa diakses siapa saja) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- Protected Routes (Harus login / punya Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cek data user yang sedang login (profil)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Nanti route Kelas, Materi, Tugas akan ditaruh di sini...
});