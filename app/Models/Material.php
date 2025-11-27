<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\ContentDetailInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Menerapkan Interface
class Material extends Model implements ContentDetailInterface
{
    // Kita matikan timestamps karena ikut parent (class_contents)
    public $timestamps = false;

    // ID tidak auto-increment, karena mengikuti ID dari ClassContent
    public $incrementing = false;

    protected $fillable = [
        'id', // ID ini adalah FK ke class_contents
        'file_path',
        'external_link',
    ];

    /**
     * Inverse Relationship ke Parent (ClassContent)
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(ClassContent::class, 'id');
    }

    /**
     * Implementasi method dari Interface
     */
    public function getDetails(): array
    {
        return [
            'type' => 'Material',
            'file' => $this->file_path,
            'link' => $this->external_link,
        ];
    }
}