<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory,HasUuids;

protected $guarded = [];
// Di dalam file App\Models\Comment.php
public function user()
{
    return $this->belongsTo(User::class);
}
}
