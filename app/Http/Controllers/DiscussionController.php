<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * GET /classrooms/{id}/discussions
     * Melihat daftar diskusi dalam kelas (Dosen & Mahasiswa).
     */
    public function index($classroomId)
    {
        // Mengambil diskusi beserta nama dosen pembuatnya
        // Diurutkan dari yang terbaru (latest) [cite: 12]
        $discussions = Discussion::where('classroom_id', $classroomId)
            ->with('teacher:id,name') 
            ->latest() 
            ->get();

        return response()->json(['data' => $discussions]);
    }

    /**
     * POST /classrooms/{id}/discussions
     * Membuat diskusi baru (Hanya Dosen).
     */
    public function store(Request $request, $classroomId)
    {
        $user = $request->user();

        // 1. Cek Role & Kepemilikan Kelas
        $classroom = ClassRoom::find($classroomId);
        if (!$classroom) {
            return response()->json(['message' => 'Classroom not found'], 404);
        }

        // Hanya teacher pemilik kelas yang boleh buat pengumuman/diskusi
        if ($user->id !== $classroom->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Validasi
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // 3. Simpan Diskusi
        $discussion = Discussion::create([
            'classroom_id' => $classroomId,
            'teacher_id' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Discussion created successfully',
            'data' => $discussion
        ], 201);
    }
}