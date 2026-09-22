<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $users = User::where('id', '!=', Auth::id())->latest()->get();

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'name'     => 'required|string|max:255',
            'nrp_nip'  => 'required|string|max:255|unique:users,nrp_nip',
            'jabatan'  => 'required|string|max:255',
            'pangkat'  => 'required|string|max:255',
            'role'     => 'required|in:admin,internal',
            'email'    => 'nullable|string|email|max:255|unique:users,email',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        User::create([
            'name'      => $request->name,
            'nrp_nip'   => $request->nrp_nip,
            'jabatan'   => $request->jabatan,
            'pangkat'   => $request->pangkat,
            'role'      => $request->role,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->back()->with('message', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'nrp_nip'  => 'required|string|max:255|unique:users,nrp_nip,' . $user->id,
            'jabatan'  => 'required|string|max:255',
            'pangkat'  => 'required|string|max:255',
            'role'     => 'required|in:admin,internal',
            'email'    => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', Rules\Password::defaults()], // Password opsional saat edit
        ]);

        $dataToUpdate = [
            'name'    => $request->name,
            'nrp_nip' => $request->nrp_nip,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'role'    => $request->role,
            'email'   => $request->email,
        ];

        // Jika form password diisi, update passwordnya
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->back()->with('message', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;

        if ($user->is_active) {
            $defaultPassword = $user->nrp_nip;
            $user->password = Hash::make($defaultPassword);
            $message = "Akun berhasil diaktifkan kembali. Password di-reset menjadi NRP/NIP pengguna: $defaultPassword";
        } else {
            $message = "Akun berhasil dinonaktifkan.";
        }

        $user->save();

        return redirect()->back()->with('message', $message);
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->withErrors('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('message', 'Akun pengguna berhasil dihapus secara permanen.');
    }
}
