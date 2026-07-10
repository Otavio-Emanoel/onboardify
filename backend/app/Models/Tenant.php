<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'primary_color', 'secondary_color', 'logo_url'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
