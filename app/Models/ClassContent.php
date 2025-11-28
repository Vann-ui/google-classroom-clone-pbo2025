<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassContent extends Model
{
    // Encapsulation: Melindungi atribut agar aman saat Mass Assignment
    protected $fillable = [
        'classroom_id',
        'created_by',
        'title',
        'description',
        'content_type', // 'material' atau 'assignment'
    ];

    /**
     * Relasi ke Child: Material
     * Menggunakan HasOne karena 1 Content punya 1 detail Material (jika tipenya material)
     */
    public function material(): HasOne
    {
        return $this->hasOne(Material::class, 'id', 'id');
    }

    /**
     * Relasi ke Child: Assignment
     */
    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class, 'id', 'id');
    }

    /**
     * Relasi ke Pembuat (User/Teacher)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }
}