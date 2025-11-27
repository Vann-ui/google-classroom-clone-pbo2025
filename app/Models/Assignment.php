<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\ContentDetailInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model implements ContentDetailInterface
{
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'due_date',
        'points',
        'attachment_path'
    ];

    protected $casts = [
        'due_date' => 'datetime', // Casting agar otomatis jadi objek Carbon
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(ClassContent::class, 'id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    // Implementasi Interface
    public function getDetails(): array
    {
        return [
            'type' => 'Assignment',
            'due_date' => $this->due_date,
            'max_points' => $this->points,
        ];
}
}