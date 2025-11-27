<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = [
        'classroom_id',
        'teacher_id',
        'title',     // [cite: 10]
        'content'    // [cite: 11]
    ];

    /**
     * Relasi ke Pembuat Diskusi (Dosen)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}