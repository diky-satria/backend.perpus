<?php

use Illuminate\Http\Request;
use App\Http\Resources\BukuResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Authorize\MeController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\CollapseController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\SuperAdmin\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Auth::loginUsingId(1);

Route::get('/bukuHome', [BukuController::class, 'bukuHome']);
Route::get('/lihatUser', [MeController::class, 'lihat']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/me', [MeController::class, '__invoke']);
    Route::get('/meUser', [MeController::class, 'meUser']);

    Route::get('/count', [AdminController::class, 'count']);
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin', [AdminController::class, 'store']);
    Route::delete('/admin/{user:id}', [AdminController::class, 'destroy']);

    Route::get('/semester', [SemesterController::class, 'index']);
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::get('/jurusan', [JurusanController::class, 'index']);
    Route::post('/jurusan', [JurusanController::class, 'store']);
    Route::delete('/jurusan/{jurusan:id}', [JurusanController::class, 'delete']);

    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
    Route::get('/mahasiswa/{mahasiswa:id}', [MahasiswaController::class, 'show']);
    Route::patch('/mahasiswa/{mahasiswa:id}', [MahasiswaController::class, 'edit']);
    Route::delete('/mahasiswa/{mahasiswa:id}', [MahasiswaController::class, 'delete']);

    Route::get('/buku', [BukuController::class, 'index']);
    Route::get('/lokasi', [LokasiController::class, 'index']);
    Route::post('/buku', [BukuController::class, 'store']);
    Route::get('/buku/{buku:id}', [BukuController::class, 'show']);
    Route::post('/buku/{buku:id}', [BukuController::class, 'edit']);
    Route::delete('/buku/{buku:id}', [BukuController::class, 'delete']);

    Route::get('/collapse', [CollapseController::class, 'index']);
    Route::get('/collapseDetail/{transaksi:id}', [CollapseController::class, 'show']);
    Route::delete('/collapse/{transaksi:id}/{detail_id}/{buku_id}', [CollapseController::class, 'delete']);
    Route::delete('/collapse/{transaksi:id}/delete', [CollapseController::class, 'destroy']);

    Route::get('/laporanHarian', [LaporanController::class, 'index']);
    Route::get('/laporanBulanan', [LaporanController::class, 'bulanan']);

    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{transaksi:id}', [TransaksiController::class, 'show']);
    Route::get('/get', [TransaksiController::class, 'get']);
    Route::get('/getBuku', [TransaksiController::class, 'getBuku']);
    Route::post('/transaksi/{kode}', [TransaksiController::class, 'store']);
    Route::get('/transaksi/{kode}/getTrans', [TransaksiController::class, 'getTrans']);
    Route::delete('/transaksi/{kode}/{id}/{kodeBuku}', [TransaksiController::class, 'delete']);
    Route::patch('/transaksi/{kode}/update', [TransaksiController::class, 'update']);
    Route::patch('/transaksi/{transaksi:id}/kembalikan', [TransaksiController::class, 'kembalikan']);
    Route::patch('/transaksi/{transaksi:id}/perpanjang', [TransaksiController::class, 'perpanjang']);

    Route::post('/mahasiswa/ubahProfil', [MahasiswaController::class, 'ubahProfil']);
    Route::patch('/mahasiswa/ubahPassword/{user:id}', [MahasiswaController::class, 'ubahPassword']);

    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/detailRiwayat/{transaksi:kode}', [UserController::class, 'riwayat']);
});
