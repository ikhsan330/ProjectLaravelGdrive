<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class DosenFolderController extends Controller
{
    public function createFolderForm()
    {
        $folders = $this->listFoldersRecursive();
        return view('documents.create-folder', compact('folders'));
    }

    public function listFoldersRecursive($parentId = null, $prefix = '')
    {
        $userId = Auth::id();
        $folders = Folder::where('user_id', $userId)
            ->where('parent_id', $parentId)
            ->get();
        $result = [];

        foreach ($folders as $folder) {
            $result[] = [
                'id' => $folder->folder_id,
                'name' => $prefix . $folder->name,
            ];

            $children = $this->listFoldersRecursive($folder->folder_id, $prefix . $folder->name . '/');
            $result = array_merge($result, $children);
        }

        return $result;
    }

}
