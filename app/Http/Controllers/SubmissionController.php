<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    /**
     * POST /assignments/{id}/submit
     * Mahasiswa mengumpulkan tugas (File atau Link).
     */
    public function store(Request $request, $assignmentId)
    {
        $user = $request->user();

        // 1. Cek Role: Hanya Student yang boleh submit
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can submit assignments'], 403);
        }

        // 2. Cek apakah Assignment ada
        $assignment = Assignment::find($assignmentId);
        if (!$assignment) {
            return response()->json(['message' => 'Assignment not found'], 404);
        }

        // 3. Validasi Input Awal (Type wajib ada)
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:file,link',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 4. Logika Polymorphism (Beda Validasi & Handling tergantung Type)
        $filePath = null;
        $link = null;

        if ($request->type === 'file') {
            // Validasi File (PDF, DOCX, PPTX) sesuai requirements
            $request->validate([
                'file' => 'required|file|mimes:pdf,docx,pptx|max:2048', // Max 2MB
            ]);

            // Proses Upload File
            if ($request->hasFile('file')) {
                // Simpan di folder 'submissions' dalam storage
                $filePath = $request->file('file')->store('submissions', 'public');
            }

        } else {
            // Validasi Link
            $request->validate([
                'link' => 'required|url',
            ]);
            $link = $request->link;
        }

        // 5. Simpan Data Submission (Create or Update)
        // Kita gunakan updateOrCreate agar mahasiswa bisa resubmit (mengganti tugasnya)
        $submission = Submission::updateOrCreate(
            [
                'assignment_id' => $assignmentId,
                'student_id' => $user->id,
            ],
            [
                'type' => $request->type,
                'file_path' => $filePath,
                'link' => $link,
                'grade' => null, // Reset nilai jika submit ulang
                'feedback' => null
            ]
        );

        return response()->json([
            'message' => 'Assignment submitted successfully',
            'data' => $submission
        ], 201);
    }

    /**
     * GET /assignments/{id}/submissions
     * (Khusus Teacher) Melihat siapa saja yang sudah kumpul tugas.
     */
    public function index(Request $request, $assignmentId)
    {
        $user = $request->user();

        // Cek Role Teacher
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Ambil submission beserta data siswanya
        $submissions = Submission::where('assignment_id', $assignmentId)
            ->with('student:id,name,email')
            ->get();

        return response()->json(['data' => $submissions]);
    }

    /**
     * PUT /submissions/{id}/grade
     * Dosen memberikan nilai dan feedback pada submission.
     */
    public function updateGrade(Request $request, $id)
    {
        $user = $request->user();

        // 1. Cek Role Teacher
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Unauthorized. Only teachers can grade.'], 403);
        }

        // 2. Cari Submission
        // Kita gunakan with('assignment') untuk memastikan submission ini valid
        $submission = Submission::with('assignment')->find($id);

        if (!$submission) {
            return response()->json(['message' => 'Submission not found'], 404);
        }

        // (Opsional) Validasi apakah Teacher ini pemilik kelas dari assignment tersebut
        // Untuk kesederhanaan tugas ini, kita cukup cek role teacher saja dulu.

        // 3. Validasi Input Nilai
        $request->validate([
            'grade' => 'required|integer|min:0|max:100', // Nilai 0 - 100
            'feedback' => 'nullable|string'
        ]);

        // 4. Update Data
        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback
        ]);

        return response()->json([
            'message' => 'Grading successful',
            'data' => $submission
        ]);
    }
}