<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Buku;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\Mahasiswa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function count()
    {
        $mahasiswa = Mahasiswa::all()->count();
        $jurusan = Jurusan::all()->count();
        $buku = Buku::all()->count();
        $transaksi = Transaksi::where('status', 0)->count();
        $chartLine = DB::SELECT("SELECT tgl_peminjaman, SUM(jumlah) jumlah FROM transaksis 
                                GROUP BY tgl_peminjaman ORDER BY tgl_peminjaman DESC LIMIT 15");
        $chartPie = DB::SELECT("SELECT bukus.judul, SUM(pinjams.jumlah) AS jumlahPinjam
                                FROM pinjams LEFT JOIN bukus ON pinjams.buku_id=bukus.id
                                GROUP BY judul ORDER BY jumlahPinjam DESC LIMIT 5");

        return response()->json([
            'chartPie' => $chartPie,
            'message' => 'hitung data',
            'mahasiswa' => $mahasiswa,
            'jurusan' => $jurusan,
            'buku' => $buku,
            'transaksi' => $transaksi,
            'chartLine' => $chartLine,
        ]);
    }

    public function index(){
        $user = User::where('role', 'admin')->orderBy('id', 'DESC')->get();
        return response()->json([
            'user' => $user
        ]);
    }

    public function store()
    {
        request()->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email'
        ],[
            'nama.required' => 'Nama harus di isi',
            'email.required' => 'Email harus di isi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar'
        ]);

        $user = User::create([
            'nama' => ucwords(request('nama')),
            'email' => request('email'),
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        return response()->json([
            'message' => 'admin berhasil ditambahkan',
            'user' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'admin berhasil dihapus'
        ]);
    }
}
