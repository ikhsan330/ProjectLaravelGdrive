<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule; // Import Rule

class AdminUserController extends Controller
{

    public function index(): View
    {
        // Ambil semua user kecuali admin
        $users = User::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
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

        if ($user->role === 'dosen') {
            $this->createDosenFolder($user->id);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat!');
    }

    // --- FITUR BARU: Menampilkan form edit (opsional, karena kita pakai modal) ---
    public function edit(User $user)
    {
        // Walaupun kita pakai modal, route ini baik untuk dimiliki
        // Anda bisa membuat view 'admin.users.edit' jika diperlukan
        return view('admin.users.edit', compact('user'));
    }

    // --- FITUR BARU: Logika untuk update user ---
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik, kecuali untuk user yang sedang diedit
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Password bersifat opsional saat update
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:dosen,kaprodi'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }


    public function destroy(User $user): RedirectResponse
    {
        // Tambahkan pengecekan agar user tidak bisa menghapus diri sendiri jika login
        if (Auth::user()->id == $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }


        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }

    protected function createDosenFolder($userId)
    {
        // ... (kode ini tidak berubah)
        $folderName = 'Penelitian-Pengabdian';
        $parentFolderId = null;

        // Anda mungkin perlu cara yang lebih baik untuk memanggil controller lain
        // Daripada `new FolderController()`, pertimbangkan menggunakan dependency injection
        // atau service class jika aplikasi semakin kompleks.
        // Untuk saat ini, ini sudah cukup.
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
}
