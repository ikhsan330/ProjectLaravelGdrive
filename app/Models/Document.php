<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 public function folder()
    {
        // Eloquent's belongsTo relationship with custom keys:
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(Folder::class, 'folderid', 'folder_id');
    }
}
