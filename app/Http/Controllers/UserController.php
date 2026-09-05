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
            'name'    => 'required|string|max:255',
            'nrp_nip' => 'required|string|max:255|unique:users,nrp_nip,' . $user->id,
            'jabatan' => 'required|string|max:255',
            'pangkat' => 'required|string|max:255',
            'role'    => 'required|in:admin,internal',
            'email'   => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'    => $request->name,
            'nrp_nip' => $request->nrp_nip,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'role'    => $request->role,
            'email'   => $request->email,
        ]);

        return redirect()->back()->with('message', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('message', "Akses masuk pengguna berhasil $status.");
    }

    public function resetPassword($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $user = User::findOrFail($id);
        // Menggunakan NRP/NIP sebagai password default sementara agar lebih relevan dengan instansi
        $defaultPassword = $user->nrp_nip;

        $user->password = Hash::make($defaultPassword);
        $user->save();

        return redirect()->back()->with('message', "Password berhasil di-reset menjadi NRP/NIP pengguna: $defaultPassword");
    }
}
