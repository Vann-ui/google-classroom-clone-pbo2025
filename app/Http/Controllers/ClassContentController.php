<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\ClassContent;
use App\Models\Material;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassContentController extends Controller
{
  /**
   * POST /classrooms/{id}/contents
   * Membuat Konten Baru (Material / Assignment) dengan konsep Inheritance.
   */
  public function store(Request $request, $classroomId)
  {
    $user = $request->user();

    // 1. Cek Otorisasi (Hanya Teacher pemilik kelas yang boleh post)
    $classroom = ClassRoom::findOrFail($classroomId);
    if ($user->id !== $classroom->teacher_id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    // 2. Validasi Input Umum (Parent)
    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'content_type' => 'required|in:material,assignment', // Tentukan tipe
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

    // 3. Mulai Database Transaction
    // Ini menjamin atomicity: Data masuk ke Parent DAN Child, atau tidak sama sekali.
    try {
      return DB::transaction(function () use ($request, $classroom, $user) {

        // A. Buat Data Parent (ClassContent)
        $content = ClassContent::create([
          'classroom_id' => $classroom->id,
          'created_by' => $user->id,
          'title' => $request->title,
          'description' => $request->description,
          'content_type' => $request->content_type,
        ]);

        // B. Logika Percabangan untuk Child (OOP Strategy)
        if ($request->content_type === 'material') {
          $this->createMaterial($request, $content->id);
        } else {
          $this->createAssignment($request, $content->id);
        }

        return response()->json([
          'message' => ucfirst($request->content_type) . ' created successfully',
          'data' => $content->load($request->content_type) // Load relasi child
        ], 201);
      });
    } catch (\Exception $e) {
      return response()->json(['message' => 'Failed to create content: ' . $e->getMessage()], 500);
    }
  }

  // Helper Private untuk Enkapsulasi Logika Material
  private function createMaterial($request, $contentId)
  {
    // Validasi khusus material
    $request->validate([
      'file_path' => 'nullable|string',
      'external_link' => 'nullable|url',
    ]);

    Material::create([
      'id' => $contentId, // ID sama dengan Parent
      'file_path' => $request->file_path,
      'external_link' => $request->external_link,
    ]);
  }

  // Helper Private untuk Enkapsulasi Logika Assignment
  private function createAssignment($request, $contentId)
  {
    // Validasi khusus assignment
    $request->validate([
      'due_date' => 'required|date',
      'points' => 'required|integer|min:0|max:100',
    ]);

    Assignment::create([
      'id' => $contentId, // ID sama dengan Parent
      'due_date' => $request->due_date,
      'points' => $request->points,
      'attachment_path' => $request->attachment_path ?? null,
    ]);
  }

  /**
   * GET /classrooms/{id}/contents
   * Melihat semua konten dalam kelas (Polymorphism reading).
   */
  public function index(Request $request, $classroomId)
  {
    // Kita ambil semua konten milik kelas tersebut
    // with('material', 'assignment') -> Eager Loading untuk efisiensi
    $contents = ClassContent::where('classroom_id', $classroomId)
      ->with(['material', 'assignment', 'author:id,name'])
      ->orderBy('created_at', 'desc')
      ->get();

    // Transformasi data agar rapi (menggunakan Interface getDetails jika perlu)
    $formatted = $contents->map(function ($content) {
      // Cek tipe dan ambil detail dari child yang sesuai
      $detail = $content->content_type === 'material' ? $content->material : $content->assignment;

      return [
        'id' => $content->id,
        'type' => $content->content_type,
        'title' => $content->title,
        'description' => $content->description,
        'author' => $content->author->name,
        // Menggabungkan detail khusus (file/link atau due_date/poin)
        'details' => $detail ? $detail->getDetails() : null, // Memanggil method Interface
        'created_at' => $content->created_at,
      ];
    });

    return response()->json(['data' => $formatted]);
  }

  /**
   * GET /contents/{id}
   * Melihat detail satu konten spesifik
   */
  public function show($id)
  {
    $content = ClassContent::with(['material', 'assignment', 'author:id,name'])->find($id);

    if (!$content) {
      return response()->json(['message' => 'Content not found'], 404);
    }

    // Tentukan detail mana yang diambil
    $child = $content->content_type === 'material' ? $content->material : $content->assignment;

    return response()->json([
      'data' => array_merge($content->toArray(), [
        'specific_detail' => $child ? $child->getDetails() : null
      ])
    ]);
  }
  /**
   * PUT /contents/{id}
   * Update Konten (Material / Assignment)
   */
  public function update(Request $request, $id)
  {
    $user = $request->user();

    // 1. Cari Konten
    $content = ClassContent::find($id);
    if (!$content) {
      return response()->json(['message' => 'Content not found'], 404);
    }

    // 2. Cek Otorisasi (Hanya Pembuat/Teacher yang boleh edit)
    if ($content->created_by !== $user->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    // 3. Validasi Input Umum
    // Kita tidak mengizinkan ganti 'content_type' agar data tidak rusak
    $request->validate([
      'title' => 'string|max:255',
      'description' => 'nullable|string',
    ]);

    // 4. Update Database Transaction
    try {
      DB::transaction(function () use ($request, $content) {
        // A. Update Parent
        $content->update($request->only(['title', 'description']));

        // B. Update Child sesuai tipe
        if ($content->content_type === 'material') {
          // Cek apakah ada request update khusus material
          if ($request->has('file_path') || $request->has('external_link')) {
            $content->material()->update($request->only(['file_path', 'external_link']));
          }
        } else {
          // Cek apakah ada request update khusus assignment
          if ($request->has('due_date') || $request->has('points')) {
            $content->assignment()->update($request->only(['due_date', 'points']));
          }
        }
      });

      return response()->json([
        'message' => 'Content updated successfully',
        'data' => $content->load($content->content_type) // Load data terbaru
      ]);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
    }
  }

  /**
   * DELETE /contents/{id}
   * Hapus Konten
   */
  public function destroy(Request $request, $id)
  {
    $user = $request->user();
    $content = ClassContent::find($id);

    if (!$content) {
      return response()->json(['message' => 'Content not found'], 404);
    }

    if ($content->created_by !== $user->id) {
      return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Hapus Parent. 
    // Karena di Migration kita set 'onDelete("cascade")', 
    // maka data di tabel materials/assignments akan otomatis terhapus.
    $content->delete();

    return response()->json(['message' => 'Content deleted successfully']);
  }
}
