<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransaksiResource;

class LaporanController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $transaksi = Transaksi::whereDate('tgl_peminjaman', [now()])->where('status', 1)->orderBy('id', 'DESC')->get();
        return response()->json([
            'data' => TransaksiResource::collection($transaksi),
            'tanggal' => date('Y-m-d')
        ]);
    }

    public function bulanan()
    {
        $from = request('from');
        $to = request('to');
        if($from && $to){
            $transaksi = Transaksi::where('status', '!=', 0)->whereBetween('tgl_peminjaman', [$from, $to])->orderBy('id', 'DESC')->get();
        }else{
            $transaksi = Transaksi::where('status', '!=', 0)->orderBy('id', 'DESC')->get(); 
        }
        return TransaksiResource::collection($transaksi);
    }
}
