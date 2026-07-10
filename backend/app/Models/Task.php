<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'module_id', 'title', 'description', 'type', 'pts', 'duration',
        'video_url', 'quiz_question', 'quiz_options', 'quiz_correct_index',
        'pdf_title', 'pdf_content_pages', 'order'
    ];

    protected $casts = [
        'quiz_options' => 'array',
        'pdf_content_pages' => 'array',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function progress()
    {
        return $this->hasMany(UserTaskProgress::class);
    }
}
