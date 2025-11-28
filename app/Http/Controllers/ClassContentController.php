<?php

namespace App\Http\Controllers;

use App\Models\ClassContent;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\ClassRoom; // Jangan lupa import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB jika mau pakai transaction (opsional)

class ClassContentController extends Controller
{
    /**
     * GET /classrooms/{id}/contents
     * Mengambil semua materi & tugas di kelas tertentu
     */
    public function index($classroomId)
    {
        // Frontend butuh data mentah relasi 'assignment' dan 'material'
        // agar bisa mengakses content.assignment.due_date, dll.
        $contents = ClassContent::where('classroom_id', $classroomId)
                    ->with(['assignment', 'material']) 
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(['data' => $contents]);
    }

    /**
     * POST /classrooms/{id}/contents
     * Membuat konten baru
     */
    public function store(Request $request, $classroomId)
    {
        // 1. Validasi Dasar
        $request->validate([
            'content_type' => 'required|in:material,assignment',
            'title' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // 2. Cek Otorisasi (Pastikan yang post adalah Guru pemilik kelas)
        $user = $request->user();
        $classroom = ClassRoom::findOrFail($classroomId);
        
        if ($user->id !== $classroom->teacher_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 3. Simpan Parent (ClassContent)
        $content = ClassContent::create([
            'classroom_id' => $classroomId,
            'created_by' => $user->id,
            'content_type' => $request->content_type,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // 4. Simpan Child (Sesuai tipe)
        // Kita pakai logika if-else sederhana agar mudah dipahami
        if ($request->content_type === 'material') {
            Material::create([
                'id' => $content->id, // Inheritance: ID sama dengan Parent
                'external_link' => $request->external_link,
                'file_path' => null 
            ]);
        } else {
            Assignment::create([
                'id' => $content->id, // Inheritance: ID sama dengan Parent
                'due_date' => $request->due_date,
                'points' => $request->points ?? 100
            ]);
        }

        return response()->json([
            'message' => 'Content created successfully',
            'data' => $content
        ], 201);
    }
    
    // Method show, update, destroy bisa kamu tambahkan belakangan jika perlu
}