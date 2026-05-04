<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(): View
    {
        $users = User::withCount(['dokumens', 'activityLogs'])->latest()->paginate(15);
        return view('pengguna.index', compact('users'));
    }

    public function create(): View
    {
        return view('pengguna.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'role'     => 'required|in:super_admin,user',
            'nip'      => 'nullable|string|max:20',
            'jabatan'  => 'nullable|string|max:100',
            'no_hp'    => 'nullable|string|max:15',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'role.required'     => 'Peran pengguna wajib dipilih.',
        ]);

        $validated['password']  = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);

        ActivityLog::log('create', "Menambah pengguna baru: {$user->name} ({$user->role_label})");

        return redirect()->route('pengguna.index')
                         ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        return view('pengguna.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'password' => ['nullable', Password::min(8)->letters()->numbers()],
            'role'     => 'required|in:super_admin,user',
            'nip'      => 'nullable|string|max:20',
            'jabatan'  => 'nullable|string|max:100',
            'no_hp'    => 'nullable|string|max:15',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        ActivityLog::log('edit', "Mengedit pengguna: {$user->name}");

        return redirect()->route('pengguna.index')
                         ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        $name = $user->name;
        $user->delete();
        ActivityLog::log('delete', "Menghapus pengguna: {$name}");

        return redirect()->route('pengguna.index')
                         ->with('success', "Pengguna {$name} berhasil dihapus.");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('edit', "Akun pengguna {$user->name} {$status}");

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }
}