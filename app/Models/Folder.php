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
        // Eloquent's hasMany relationship with custom keys:
        // hasMany(RelatedModel, foreign_key_on_related_model, local_key_on_this_model)
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

 public function getTotalFileCount()
    {
        // Hitung dokumen di folder saat ini
        $count = $this->documents->count();

        // Tambahkan hitungan dari setiap anak secara rekursif
        // Kita cek 'relationLoaded' untuk memastikan tidak terjadi query N+1
        if ($this->relationLoaded('childrenRecursive')) {
            foreach ($this->childrenRecursive as $child) {
                $count += $child->getTotalFileCount();
            }
        }

        return $count;
    }

        public function childrenRecursive()
    {
       return $this->subfolders()->with('childrenRecursive');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'folder_id');
    }

     public function getAllDescendantIdsAndSelf()
    {
        $ids = collect([$this->id]);

        foreach ($this->children as $child) {
            $ids = $ids->merge($child->getAllDescendantIdsAndSelf());
        }

        return $ids;
    }
}
