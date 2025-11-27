<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
  protected $table = 'classrooms'; // Memastikan nama tabel benar

  protected $fillable = [
    'teacher_id',
    'name',
    'description',
    'code',
  ];

  // Relasi: Kelas dimiliki 1 Teacher
  public function teacher()
  {
    return $this->belongsTo(User::class, 'teacher_id');
  }

  // Relasi: Kelas punya banyak konten
  public function contents()
  {
    return $this->hasMany(ClassContent::class);
  }
  // Kelas punya banyak mahasiswa (anggota)
public function students()
{
    return $this->belongsToMany(User::class, 'classroom_user', 'classroom_id', 'user_id')
                ->where('role', 'student'); // Filter hanya ambil yang student
}
}
