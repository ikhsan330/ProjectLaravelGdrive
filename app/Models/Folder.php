<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(Document::class, 'folderid', 'folder_id');
    }

    public function subfolders()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

