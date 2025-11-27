<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ClassRoomController extends Controller
{
    /**
     * GET /classrooms
     * Menampilkan daftar kelas.
     * - Jika Teacher: menampilkan kelas yang dia buat.
     * - Jika Student: menampilkan semua kelas (atau bisa difilter nanti).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // Guru hanya melihat kelas miliknya sendiri
            $classrooms = ClassRoom::where('teacher_id', $user->id)->get();
        } else {
            // Siswa melihat semua kelas yang tersedia (Logic bisa disesuaikan)
            $classrooms = ClassRoom::with('teacher:id,name')->get();
        }

        return response()->json([
            'message' => 'List of classrooms retrieved successfully',
            'data' => $classrooms
        ]);
    }

    /**
     * POST /classrooms
     * Membuat kelas baru (Hanya Teacher).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // 1. Cek Otorisasi: Hanya Teacher yang boleh buat kelas
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Unauthorized. Only teachers can create classrooms.'], 403);
        }

        // 2. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 3. Buat Kelas
        $classroom = ClassRoom::create([
            'teacher_id' => $user->id, // Ambil ID dari token login
            'name' => $request->name,
            'description' => $request->description,
            'code' => Str::upper(Str::random(6)), // Generate kode unik 6 karakter
        ]);

        return response()->json([
            'message' => 'Classroom created successfully',
            'data' => $classroom
        ], 201);
    }

    /**
     * GET /classrooms/{id}
     * Melihat detail satu kelas spesifik.
     */
    public function show($id)
    {
        // Load kelas beserta data gurunya
        $classroom = ClassRoom::with('teacher:id,name')->find($id);

        if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        return response()->json(['data' => $classroom]);
    }

    /**
     * PUT /classrooms/{id}
     * Mengupdate info kelas (Hanya Pemilik Kelas).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $classroom = ClassRoom::find($id);

        if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        // Cek apakah user ini adalah pemilik kelas tersebut
        if ($user->id !== $classroom->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
        ]);

        $classroom->update($request->only(['name', 'description']));

        return response()->json([
            'message' => 'Classroom updated successfully',
            'data' => $classroom
        ]);
    }

    /**
     * DELETE /classrooms/{id}
     * Menghapus kelas (Hanya Pemilik Kelas).
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $classroom = ClassRoom::find($id);

        if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        if ($user->id !== $classroom->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $classroom->delete();

        return response()->json(['message' => 'Classroom deleted successfully']);
    }
}