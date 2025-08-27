<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Menampilkan halaman profil pengguna yang sedang login
    public function index()
    {
        // Ambil data pengguna yang sedang login
        $user = auth()->user();  // Mendapatkan pengguna yang sedang login

        return view('profile.index', compact('user'));
    }

    // Menampilkan halaman edit profil
    public function edit(User $user)
    {
        // Pastikan hanya operator atau admin yang bisa mengaksesnya
        // Misalnya, jika user yang login adalah operator atau admin yang memiliki akses ke profil ini
        return view('profile.edit', compact('user'));
    }

    // Memproses pembaruan data profil
    public function update(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'nip' => 'required|string|max:255',
            'nama_pegawai' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Maksimal ukuran foto 2MB
            'old_password' => 'nullable|string|min:6', // Validasi password lama
            'new_password' => 'nullable|string|min:6|confirmed', // Validasi password baru
        ]);

        // Verifikasi apakah password lama sesuai
        if ($request->filled('old_password') && !Hash::check($request->old_password, $user->password)) {
            // Jika password lama tidak cocok, kembali dengan error
            return back()->withErrors(['old_password' => 'Password lama tidak cocok.']);
        }

        // Update data pengguna
        $user->nip = $request->nip;
        $user->nama_pegawai = $request->nama_pegawai;
        $user->jabatan = $request->jabatan;
        $user->ruangan = $request->ruangan;

        // Proses upload foto jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::delete('public/user/' . $user->foto); // Hapus foto lama dari storage
            }

            // Simpan foto baru
            $path = $request->file('foto')->store('user', 'public');
            $user->foto = basename($path);
        }

        // Jika password baru diisi, update password
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password); // Enkripsi password baru
        }

        // Simpan perubahan
        $user->save();

        // Redirect kembali ke halaman profil setelah sukses
        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui');
    }

    public function indexAdmin()
    {
        $user = Auth::user();
        return view('profile.index_admin', compact('user'));
    }

    public function editAdmin(User $user)
    {
        // Cek apakah user yang sedang login adalah user yang ingin diedit (opsional)
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return view('profile.edit_admin', compact('user'));
    }

    public function updateAdmin(Request $request, User $user)
    {
        // Cek apakah user yang sedang login adalah user yang ingin diupdate
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $request->validate([
            'nip' => 'required|string|max:255|unique:user,nip,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|max:2048',
        ]);

        $user->nip = $request->nip;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('foto_user'), $namaFoto);
            $user->foto = $namaFoto;
        }

        $user->save();

        return redirect()->route('profile.admin.index')->with('success', 'Profil berhasil diperbarui');
    }
}
