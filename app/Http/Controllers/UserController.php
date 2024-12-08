<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::orderBy('nama_pegawai', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:user,nip',
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            ],
            'konfirmasi_password' => 'required|same:password',
            'level' => 'required|string|in:admin,operator',
            'nama_pegawai' => 'required|string|max:255',
            'jabatan' => 'required|string',
            'ruangan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $filename = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'Foto_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/user', $filename);
        }

        User::create([
            'nip' => $request->nip,
            'password' => bcrypt($request->password),
            'level' => $request->level,
            'nama_pegawai' => $request->nama_pegawai,
            'jabatan' => $request->jabatan,
            'ruangan' => $request->ruangan,
            'foto' => $filename,
        ]);

        return redirect()->route('user.index')->with('success', 'Data berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        // Validasi input
        $validated = $request->validate([
            'nip' => 'required|unique:user,nip,' . $user->id,
            'nama_pegawai' => 'required',
            'jabatan' => 'required',
            'ruangan' => 'required',
            'level' => 'required',
            // Validasi password hanya jika password baru diisi
            'password' => 'nullable|min:6|confirmed',
            'konfirmasi_password' => 'nullable|same:password',
        ]);
    
        // Update data user
        $user->nip = $request->nip;
        $user->nama_pegawai = $request->nama_pegawai;
        $user->jabatan = $request->jabatan;
        $user->ruangan = $request->ruangan;
        $user->level = $request->level;
    
        // Jika password diisi, update password
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
    
        // Simpan perubahan
        $user->save();
    
        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui!');
    }
    
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->level === 'admin') {
                return redirect()->route('user.index')->with('error', 'User dengan level admin tidak dapat dihapus.');
            }

            if ($user->foto) {
                Storage::delete('public/user/' . $user->foto);
            }

            $user->delete();

            return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === "23000") {
                return redirect()->route('user.index')->with('error', 'User tidak dapat dihapus karena terkait dengan data lain.');
            }

            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan saat menghapus user.');
        }
    }
}
