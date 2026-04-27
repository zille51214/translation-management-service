<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'locale', 'value'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}