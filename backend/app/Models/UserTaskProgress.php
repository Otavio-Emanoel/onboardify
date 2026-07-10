<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTaskProgress extends Model
{
    protected $table = 'user_task_progress';

    protected $fillable = ['user_id', 'task_id', 'completed', 'completed_at'];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
