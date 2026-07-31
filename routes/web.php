<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SambutCon;
use App\Http\Controllers\PengaturanCon;
use App\Http\Controllers\GuruCon;
use App\Http\Controllers\WaliCon;
use App\Http\Controllers\SiswaCon;
use App\Http\Controllers\InfaqCon;
use App\Http\Controllers\TagihanCon;
use App\Http\Controllers\PembayaranCon;
use App\Http\Controllers\InstalCon;



/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "Koneksi database berhasil";
    } catch (\Exception $e) {
        return "Koneksi database gagal: " . $e->getMessage();
    }
});

//InstalCon
//Route::get('/install', [InstalCon::class, 'index']);
//Route::post('/install', [InstalCon::class, 'install']);

//SambutCon
Route::get('/', [SambutCon::class, 'masukwali'])->name('masukwali');
//Route::get('/', function () {    return 'OK';});
Route::get('/masukguru', [SambutCon::class, 'masukguru'])->name('masukguru');
Route::post('/cekmasukguru', [GuruCon::class, 'cekmasukguru'])->name('cekmasukguru');
Route::get('/keluar', [SambutCon::class, 'keluar'])->name('keluar');

//WaliCon
Route::post('/ruangwali', [WaliCon::class, 'ruangwali']);


Route::middleware(['cekmasukguru'])->group(function () {

    //PengaturanCon
    Route::get('/pengaturan', [PengaturanCon::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan/banner', [PengaturanCon::class, 'updateBanner']);
    Route::get('/pengaturan/pamflet', [PengaturanCon::class, 'ubahpamflet']);

    //GuruCon

    Route::post('/ruangguru', [GuruCon::class, 'ruangguru']);
    Route::get('/ruangguru', [GuruCon::class, 'ruangguru']);
    Route::get('/dataguru', [GuruCon::class, 'dataguru'])->name('dataguru');
    Route::get('/tambahguru', [GuruCon::class, 'dataguru_tambah'])->name('dataguru_tambah');
    Route::post('/simpanguru', [GuruCon::class, 'dataguru_simpan'])->name('dataguru_simpan');
    Route::post('/hapusguru/{id}', [GuruCon::class, 'hapus']);
    Route::get('/editguru/{id}', [GuruCon::class, 'edit']);
    Route::post('/updateguru/{id}', [GuruCon::class, 'update']);



    //SiswaCon
    Route::get('/datasiswa', [SiswaCon::class, 'index']);
    Route::get('/datasiswa/angkatan/{id}', [SiswaCon::class, 'byAngkatan'])->name('siswa.angkatan');

    Route::get('/tambahangkatan', [SiswaCon::class, 'angkatan_tambah'])->name('angkatan_tambah');
    Route::post('/simpanangkatan', [SiswaCon::class, 'angkatan_simpan'])->name('angkatan_simpan');

    Route::post('/naikkelas', [SiswaCon::class, 'naikkelas']);


    Route::get('/tambahsiswa/{id}', [SiswaCon::class, 'datasiswa_tambah']);
    Route::post('/simpansiswa', [SiswaCon::class, 'datasiswa_simpan']);
    Route::post('/hapussiswa/{id}', [SiswaCon::class, 'hapus']);
    Route::get('/editsiswa/{id}', [SiswaCon::class, 'edit']);
    Route::post('/updatesiswa/{id}', [SiswaCon::class, 'update']);

    //SPMB
    Route::get('/spmb/{id}', [SiswaCon::class, 'spmb']);
    Route::get('/apin/{id}', [SiswaCon::class, 'apin']);
    Route::post('/spmb/import', [SiswaCon::class, 'importSpmb']);
    Route::post('/apin/import', [SiswaCon::class, 'importApin']);

    //InfaqCon
    Route::get('/datainfaq', [InfaqCon::class, 'index']);
    Route::get('/datainfaq/tahunajaran/{id}', [InfaqCon::class, 'byTahunajaran'])->name('infaq.tahunajaran');

    Route::get('/tambahinfaq/{id}', [InfaqCon::class, 'datainfaq_tambah']);
    Route::post('/simpaninfaq', [InfaqCon::class, 'datainfaq_simpan']);
    Route::post('/hapusinfaq/{id}', [InfaqCon::class, 'hapus']);
    Route::get('/editinfaq/{id}', [InfaqCon::class, 'edit']);
    Route::post('/updateinfaq/{id}', [InfaqCon::class, 'update']);

    //TagihanCon
    Route::get('/datatagihan', [TagihanCon::class, 'index']);
    Route::get('/tambahtagihan', [TagihanCon::class, 'tagihan_tambah']);
    Route::post('/simpantagihan', [TagihanCon::class, 'tagihan_simpan']);
    //Route::get('/datatagihan/pdf/{kelas}', [TagihanCon::class, 'pdf']);
    Route::get('/datatagihan/acara/{id}', [TagihanCon::class, 'byAcara']);
    //Route::get('/datatagihan/pdf/{acara}', [TagihanCon::class, 'pdf']);
    Route::get('/datatagihan/pdf/{acara}/{kelas}', [TagihanCon::class, 'pdf']);
    Route::post('/hapusacara/{id}', [TagihanCon::class, 'hapus']);

    //PembayaranCon
    Route::get('/datapembayaran', [PembayaranCon::class, 'index']);
    Route::get('/tambahpembayaran', [PembayaranCon::class, 'pembayaran_tambah']);
    Route::get('/search-siswa', [PembayaranCon::class, 'search']);
    Route::get('/get-infaq/{id_siswa}', [PembayaranCon::class, 'getInfaq']);
    Route::post('/simpanpembayaran', [PembayaranCon::class, 'pembayaran_simpan']);
    Route::get('/datapembayaran/kwitansi/{id}', [PembayaranCon::class, 'kwitansi']);
    Route::get('/kwitansi/pdf/{id}', [PembayaranCon::class, 'pdfkwitansi']);
    Route::post('/hapuspembayaran/{id}', [PembayaranCon::class, 'hapus']);

    Route::get('/transaksiberhasil', [PembayaranCon::class, 'transaksiBerhasil']);

    Route::get('/cek-fonnte', function () {
        try {
            $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token'),
            ])->timeout(5)->post('https://api.fonnte.com/device');

            $json = $response->json();

            if (($json['device_status'] ?? null) === 'connect') {
                return response()->json(['connected' => true]);
            }

            return response()->json(['connected' => false]);

        } catch (\Exception $e) {
            return response()->json(['connected' => false]);
        }
    });

});