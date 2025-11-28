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
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // GURU: Hanya melihat kelas yang dia BUAT
            $classrooms = ClassRoom::where('teacher_id', $user->id)->get();
        } else {
            // SISWA: Hanya melihat kelas yang dia JOIN
            // Menggunakan relasi joinedClasses yang sudah kita buat di Model User
            $classrooms = $user->joinedClasses()->with('teacher:id,name')->get();
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

        // 1. Cek Otorisasi
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Unauthorized. Only teachers can create classrooms.'], 403);
        }

        // 2. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // 3. Generate Kode Unik (Looping untuk memastikan tidak ada duplikat)
        do {
            $randomCode = Str::upper(Str::random(6));
        } while (ClassRoom::where('code', $randomCode)->exists());

        // 4. Buat Kelas
        $classroom = ClassRoom::create([
            'teacher_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'code' => $randomCode,
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

    /**
     * POST /classrooms/join
     * Mahasiswa gabung kelas pakai Kode Unik.
     */
    public function join(Request $request)
    {
        $user = $request->user();

        // 1. Hanya student yang boleh join
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can join classes'], 403);
        }

        // 2. Validasi Kode
        $request->validate([
            'code' => 'required|string|exists:classrooms,code'
        ]);

        // 3. Cari Kelas berdasarkan Kode
        $classroom = ClassRoom::where('code', $request->code)->first();

        // 4. Cek apakah sudah gabung duluan?
        if ($user->joinedClasses()->where('classroom_id', $classroom->id)->exists()) {
            return response()->json(['message' => 'You already joined this class'], 409);
        }

        // 5. GABUNGKAN!
        $user->joinedClasses()->attach($classroom->id);

        return response()->json([
            'message' => 'Successfully joined class',
            'data' => $classroom
        ]);
    }
}