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

        // Simpan user sementara tanpa kode_user
        $user = User::create([
            'nip' => $request->nip,
            'password' => bcrypt($request->password),
            'level' => $request->level,
            'nama_pegawai' => $request->nama_pegawai,
            'jabatan' => $request->jabatan,
            'ruangan' => $request->ruangan,
            'foto' => $filename,
        ]);

        // Buat kode_user berdasarkan jabatan dan ID
        // Mapping prefix jabatan
        $jabatanPrefix = match ($user->jabatan) {
            'Kepala Apotik' => 'KPT',
            'Apoteker' => 'APT',
            'Staf' => 'STF',
            default => 'USR',
        };

        // Mapping inisial ruangan
        $ruanganInisial = match ($user->ruangan) {
            'Instalasi Farmasi' => 'IF',
            'puskesmas Kaligangsa' => 'KG',
            'puskesmas Margadana' => 'MG',
            'puskesmas Tegal Barat' => 'TB',
            'puskesmas Debong Lor' => 'DL',
            'puskesmas Tegal Timur' => 'TT',
            'puskesmas Slerok' => 'SL',
            'puskesmas Tegal Selatan' => 'TS',
            'puskesmas Bandung' => 'BD',
            default => 'XX',
        };

        // Jika admin, tetap gunakan ADM
        if ($user->level === 'admin') {
            $jabatanPrefix = 'ADM';
            // Inisial bisa tetap IF atau kosong jika tidak perlu
        }

        // Format: PREFIX-INISIAL-0001
        $kode = $jabatanPrefix . '-' . $ruanganInisial . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $user->kode_user = $kode;
        $user->save();


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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nip' => 'required|unique:user,nip,' . $user->id,
            'nama_pegawai' => 'required',
            'jabatan' => 'required',
            'ruangan' => 'required',
            'level' => 'required',
            // Validasi password hanya jika password baru diisi
            'password' => 'nullable|min:6|confirmed',
            'konfirmasi_password' => 'nullable|same:password',
        ]);


        // Jika ada file foto baru, hapus foto lama dan simpan yang baru
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete('public/user/' . $user->foto);
            }
            $foto = $request->file('foto');
            $filename = 'FTM_' . time() . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/user/', $filename);
            $user->foto = $filename;
        }

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

            // Cek jika user adalah admin
            if ($user->level === 'admin') {
                return redirect()->route('user.index')->with('error', 'User dengan level admin tidak dapat dihapus.');
            }

            // // Cek relasi jika ada data terkait (contoh relasi transaksi)
            // if ($user->transaksi()->exists() || $user->stokMasuk()->exists()) {
            //     return redirect()->route('user.index')->with('error', 'User tidak dapat dihapus karena terkait dengan data lain.');
            // }

            // Hapus foto jika ada
            if ($user->foto) {
                Storage::delete('public/user/' . $user->foto);
            }

            // Hapus user
            $user->delete();

            //     return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
            // } catch (\Illuminate\Database\QueryException $e) {
            //     // Tangkap error jika user memiliki constraint relasi yang tidak terdefinisi sebelumnya
            //     if ($e->getCode() === "23000") { // Kode SQLSTATE untuk integrity constraint violation
            //         return redirect()->route('user.index')->with('error', 'User tidak dapat dihapus karena memiliki relasi data.');
            //     }

            //     return redirect()->route('user.index')->with('error', 'Terjadi kesalahan saat menghapus user.');
            // } catch (\Exception $e) {
            //     return redirect()->route('user.index')->with('error', 'Terjadi kesalahan tidak terduga.');
            // }
            return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === "23000") {
                return redirect()->route('user.index')->with('error', 'User tidak dapat dihapus karena terkait dengan data lain.');
            }

            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan saat menghapus user.');
        }
    }
}
