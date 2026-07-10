<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['tenant_id', 'title', 'description', 'week', 'order'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }
}
