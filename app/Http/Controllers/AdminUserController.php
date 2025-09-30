<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Folder; // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log; // Add this line

class AdminUserController extends Controller
{

    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:dosen,kaprodi'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Check if the user's role is 'dosen'
        if ($user->role === 'dosen') {
            $this->createDosenFolder($user->id);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat!');
    }

    protected function createDosenFolder($userId)
    {
        $folderName = 'Penelitian-Pengabdian';
        $parentFolderId = null; // Assuming this is a root-level folder

        $folderController = new FolderController();
        $newFolderId = $folderController->createFolder($folderName, $parentFolderId);

        if (!$newFolderId) {
            Log::error("Failed to create Google Drive folder for new user (ID: {$userId})");
            return;
        }

        try {
            $folder = new Folder;
            $folder->name = $folderName;
            $folder->folder_id = $newFolderId;
            $folder->parent_id = $parentFolderId;
            $folder->user_id = $userId;
            $folder->save();
        } catch (\Exception $e) {
            Log::error("Failed to save folder record to database for user (ID: {$userId}): " . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }
}
