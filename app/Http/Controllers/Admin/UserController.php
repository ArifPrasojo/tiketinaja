<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['bookings']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:customer,manager,admin'],
        ]);

        // Guard: admin tidak bisa ubah role diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat mengubah role akun Anda sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role user berhasil diperbarui menjadi ' . ucfirst($request->role) . '.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Jangan hapus admin lain tanpa konfirmasi lebih lanjut (minimal protection)
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
