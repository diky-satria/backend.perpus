<?php

namespace App\Http\Controllers\Admin;

use App\Models\Buku;
use App\Models\Pinjam;
use App\Models\Mahasiswa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MahasiswaResource;
use App\Http\Resources\TransaksiResource;
use App\Http\Resources\PeminjamanResource;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::where('status', 1)->orderBy('id', 'DESC')->get();
        return TransaksiResource::collection($transaksi);
    }

    public function show(Transaksi $transaksi)
    {   
        $peminjaman = $transaksi->pinjams()->get();
        return response()->json([
            'data' => new TransaksiResource($transaksi),
            'peminjaman' => PeminjamanResource::collection($peminjaman)
        ]);
    }

    public function get()
    {
        $id = Transaksi::max('id') + 1;

        $kode = kode_random(6) . $id;
        date_default_timezone_set('Asia/Jakarta');
        $tgl_peminjaman = date('Y-m-d');
        $tgl_pengembalian = date('Y-m-d', time()+60*60*24*8);
        $mahasiswa = Mahasiswa::orderBy('nama', 'ASC')->get();

        return response()->json([
            'kode' => $kode,
            'tgl_peminjaman' => $tgl_peminjaman,
            'tgl_pengembalian' => $tgl_pengembalian,
            'mahasiswa' => MahasiswaResource::collection($mahasiswa)
        ]);
    }

    public function getBuku()
    {
        $buku = Buku::where('jumlah', '>', 0)->orderBy('judul', 'ASC')->get();
        return BukuResource::collection($buku);
    }

    public function store($kode)
    {
        $tran = Transaksi::where('kode', $kode)->first();
        request()->validate([
            'kode' => 'required',
            'tgl_peminjaman' => 'required',
            'tgl_pengembalian' => 'required',
            'buku_id' => 'required'
        ],[
            'kode.required' => 'Kode harus di isi',
            'tgl_peminjaman.required' => 'Tanggal harus di isi',
            'tgl_pengembalian.required' => 'Tanggal harus di isi',
            'buku_id.required' => 'Buku harus di pilih'
        ]);


        if($tran){
            $tran->pinjams()->create([
                'buku_id' => request('buku_id')
            ]);
            $buku = Buku::where('id', request('buku_id'))->first();
            $buku->update([
                'jumlah' => $buku->jumlah - 1,
            ]);
        }else{
            Transaksi::create([
                'kode' => request('kode'),
                'tgl_peminjaman' => request('tgl_peminjaman'),
                'tgl_pengembalian' => request('tgl_pengembalian'),
                'status' => 0,
                'petugas' => Auth::user()->nama
            ])->pinjams()->create([
                'buku_id' => request('buku_id')
            ]);
            $buku = Buku::where('id', request('buku_id'))->first();
            $buku->update([
                'jumlah' => $buku->jumlah - 1
            ]);
        }

        return response()->json([
            'message' => 'transaksi berhasil ditambahkan'
        ]);
    }

    public function getTrans($kode)
    {
        $transaksi = Transaksi::where('kode', $kode)->first();
        if($transaksi){
            $peminjaman = $transaksi->pinjams()->get();
            $count = $transaksi->pinjams()->get()->count();
            return response()->json([
                'peminjaman' => PeminjamanResource::collection($peminjaman),
                'count' => $count
            ]);
        }else{
            return response()->json([
                'message' => 'anda belum memilih buku'
            ]);
        }
    }

    public function delete($kode, $id, $kodeBuku)
    {
        $transaksi = Transaksi::where('kode', $kode)->first();
        $transaksi_id = $transaksi->id;
        $trans = Transaksi::find($transaksi_id);
        $trans->pinjams()->detach($id);
        $pinjam = Pinjam::find($id);
        $pinjam->delete();

        $buku = Buku::where('kode', $kodeBuku)->first();
        $buku->update([
            'jumlah' => $buku->jumlah + 1
        ]);

        return response()->json([
            'message' => 'buku berhasil dihapus'
        ]);
    }

    public function update($kode)
    {
        request()->validate([
            'mahasiswa_id' => 'required'
        ],[
            'mahasiswa_id.required' => 'Mahasiswa harus dipilih'
        ]);
        
        $transaksi = Transaksi::where('kode', $kode)->first();
        $jumlah = $transaksi->pinjams()->get()->count();
        $transaksi->update([
            'mahasiswa_id' => request('mahasiswa_id'),
            'jumlah' => $jumlah,
            'status' => 1
        ]);

        return response()->json([
            'message' => 'update berhasil'
        ]);
    }

    public function kembalikan(Transaksi $transaksi)
    {
        $transaksi->update([
            'status' => 2
        ]);

        return response()->json([
            'message' => 'transaksi berhasil dikembalikan'
        ]);
    }

    public function perpanjang(Transaksi $transaksi)
    {
        $tgl_peminjaman = date('Y-m-d');
        $tgl_pengembalian = date('Y-m-d', time()+60*60*24*8);

        $transaksi->update([
            'tgl_peminjaman' => $tgl_peminjaman,
            'tgl_pengembalian' => $tgl_pengembalian
        ]);

        return response()->json([
            'message' => 'berhasil melakukan perpanjangan peminjaman'
        ]);
    }
}
