<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\ClassContentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\DiscussionController;

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
  // --- API Resource untuk ClassRoom ---
  // Ini otomatis membuat route index, store, show, update, destroy
  Route::apiResource('classrooms', \App\Http\Controllers\ClassRoomController::class);

  // Route untuk membuat konten dalam kelas tertentu
  Route::post('/classrooms/{classroom}/contents', [ClassContentController::class, 'store']);

  // Route untuk melihat semua konten dalam kelas tertentu
  Route::get('/classrooms/{classroom}/contents', [ClassContentController::class, 'index']);

  // Route untuk melihat detail konten spesifik (tanpa perlu ID kelas)
  Route::get('/contents/{id}', [ClassContentController::class, 'show']);

  // Route Mahasiswa Submit Tugas
  Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store']);

  // Route Guru Lihat List Submission di Tugas tertentu
  Route::get('/assignments/{assignment}/submissions', [SubmissionController::class, 'index']);

  // Route Guru Memberi Nilai (Grading)
  Route::put('/submissions/{id}/grade', [SubmissionController::class, 'updateGrade']);

  // --- Route Diskusi ---
  Route::get('/classrooms/{classroom}/discussions', [DiscussionController::class, 'index']);
  Route::post('/classrooms/{classroom}/discussions', [DiscussionController::class, 'store']);
  // Nanti route Kelas, Materi, Tugas akan ditaruh di sini...
});
