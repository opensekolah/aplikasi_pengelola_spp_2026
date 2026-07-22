<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Angkatan;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\Tahunajaran;
use App\Models\Infaq;
use App\Models\Pembayaran;
use App\Models\Jenisbayar;
use App\Models\Guru;
use App\Models\Countertransaksi;
use Barryvdh\DomPDF\Facade\Pdf;
//use App\Models\Pengaturan;

class PembayaranCon extends Controller
{


    public function index()
    {
        //$pembayaran = Pembayaran::all();

        /*$pembayaran = Pembayaran::join('siswa', 'pembayaran.id_siswa', '=', 'siswa.id')
            ->join('guru', 'pembayaran.id_guru', '=', 'guru.id')
            ->join('jenisbayar', 'pembayaran.id_jenisbayar', '=', 'jenisbayar.id')
            ->select(
                'siswa.name as nama_siswa',
                'guru.name as nama_petugas',
                'jenisbayar.name as jenis_bayar',
                'pembayaran.*'

            )
            ->orderByDesc('pembayaran.tanggal_pembayaran')
            ->get();*/

        $pembayaran = Pembayaran::join('siswa', 'pembayaran.id_siswa', '=', 'siswa.id')
            ->join('guru', 'pembayaran.id_guru', '=', 'guru.id')
            ->join('jenisbayar', 'pembayaran.id_jenisbayar', '=', 'jenisbayar.id')
            ->select(
                'pembayaran.*',
                'siswa.name as nama_siswa',
                'guru.name as nama_petugas',
                'jenisbayar.name as jenis_bayar'
            )
            ->orderByDesc('pembayaran.tanggal_pembayaran')
            ->get()
            ->groupBy('id_transaksi')
            ->map(function ($group) {

                return [
                    'tanggal_pembayaran' => $group->first()->tanggal_pembayaran,
                    'id_transaksi' => $group->first()->id_transaksi,
                    'nama_siswa' => $group->first()->nama_siswa,
                    'nama_petugas' => $group->first()->nama_petugas,
                    'jenis_bayar' => $group->first()->jenis_bayar,
                    'tanggal' => $group->first()->created_at,


                    'total_bayar' => $group->sum('infaq_harga'),


                    'detail' => $group
                ];
            });


        $data = [
            'title' => 'Pembayaran',
            'pembayaran' => $pembayaran,
        ];

        //dd($data);

        return view('rg-pembayaran', compact('data'));
    }

    public function search(Request $request)
    {
        $keyword = $request->q;

        $data = Siswa::join('angkatan', 'siswa.id_angkatan', '=', 'angkatan.id')
            ->join('kelompok', 'angkatan.id_kelompok', '=', 'kelompok.id')
            ->where('siswa.name', 'like', '%' . $keyword . '%')
            ->select(
                'siswa.id as id_siswa',
                'siswa.name as nama_siswa',
                'siswa.id_angkatan',
                'kelompok.id as id_kelompok',
                'kelompok.name as nama_kelas'
            )
            ->limit(10)
            ->get();

        return response()->json($data);
        /*return response()->json([
            [
                'id_siswa' => 1,
                'nama_siswa' => 'Budi',
                'nama_kelas' => 'Kelas 7'
            ]
        ]);*/
    }


    public function getInfaq($id_siswa)
    {
        try {

            $siswa = Siswa::find($id_siswa);



            /*if (!$siswa) {
                return response()->json([]);
            }*/

            // ambil infaq sesuai angkatan dan tahun ajaran
            /*
            $infaq = DB::table('infaq')
                ->where('id_angkatan', $siswa->id_angkatan)
                ->get();
                */
            $tahunajaran = Tahunajaran::orderBy('created_at', 'desc')->first();


            $infaq = DB::table('infaq')
                ->where('id_angkatan', $siswa->id_angkatan)
                ->where('id_tahunajaran', $tahunajaran->id)
                ->get();



            // ambil total pembayaran per infaq
            $pembayaran = DB::table('pembayaran')
                ->select('infaq_id', DB::raw('SUM(infaq_harga) as total'))
                ->where('id_siswa', $id_siswa)
                ->groupBy('infaq_id')
                ->get()
                ->keyBy('infaq_id');

            $result = $infaq->map(function ($item) use ($pembayaran) {

                $sudahBayar = $pembayaran[$item->id]->total ?? 0;

                $sisa = $item->harga - $sudahBayar;


                if ($sisa <= 0) {
                    return null;
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'harga' => $sisa
                ];
            })
                ->filter()
                ->values();

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pembayaran_tambah()
    {
        /*$angkatan = Angkatan::find();*/
        $kelompok_id = ['2', '3', '4'];

        $siswa = Siswa::whereIn('id_angkatan', function ($query) use ($kelompok_id) {
            $query->select('id')
                ->from('angkatan')
                ->whereIn('id_kelompok', $kelompok_id);
        })->get();

        $jenisbayar = Jenisbayar::all();

        //$infaq = Infaq::where('id_angkatan', $angkatan_id);

        //dd($siswa);

        $data = [
            'title' => 'Tambah Pembayaran',
            'jenisbayar' => $jenisbayar
        ];



        return view('rg-pembayaran-tambah', compact('data'));
    }

    public function pembayaran_simpan(Request $request)
    {

        $id_kelompok = str_pad($request->id_angkatan, 2, '0', STR_PAD_LEFT);
        $id_siswa = str_pad($request->id_siswa, 2, '0', STR_PAD_LEFT);

        $counter = DB::table('counter_transaksi')->first();

        $nomor_transaksi = $counter->last_number + 1;


        DB::table('counter_transaksi')->update([
            'last_number' => $nomor_transaksi
        ]);

        $counterFormatted = str_pad($nomor_transaksi, 3, '0', STR_PAD_LEFT);


        $id_transaksi = $id_kelompok . $id_siswa . $counterFormatted;




        DB::beginTransaction();

        try {

            $id_guru = session('user_id');

            if (!$request->bayar_infaq) {
                return back()->with('error', 'Tidak ada data pembayaran');
            }

            foreach ($request->bayar_infaq as $id_infaq => $nominal) {

                if ($nominal > 0) {

                    $infaq = DB::table('infaq')->where('id', $id_infaq)->first();

                    DB::table('pembayaran')->insert([
                        'id_transaksi' => $id_transaksi,
                        'id_siswa' => $request->id_siswa,
                        'id_angkatan' => $request->id_angkatan,
                        'infaq_id' => $id_infaq,

                        'infaq_name' => $infaq->name ?? null,
                        'infaq_harga' => $nominal,

                        'id_guru' => $id_guru,
                        'id_jenisbayar' => $request->jenis_bayar,

                        //'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect('/datapembayaran')->with('success', 'Pembayaran berhasil disimpan');

        } catch (\Exception $e) {

            DB::rollBack();

            dd($e->getMessage());
        }
    }

    public function hapus($id_transaksi)
    {
        $deleted = Pembayaran::where('id_transaksi', $id_transaksi)->delete();

        if ($deleted == 0) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil dihapus');
    }

    public function kwitansi($id_transaksi)
    {
        // ambil semua pembayaran dalam 1 transaksi
        $pembayaran = Pembayaran::join('siswa', 'pembayaran.id_siswa', '=', 'siswa.id')
            ->join('guru', 'pembayaran.id_guru', '=', 'guru.id')
            ->join('jenisbayar', 'pembayaran.id_jenisbayar', '=', 'jenisbayar.id')
            ->where('pembayaran.id_transaksi', $id_transaksi)
            ->select(
                'pembayaran.*',
                'siswa.name as nama_siswa',
                'guru.name as nama_petugas',
                'jenisbayar.name as jenis_bayar'
            )
            ->get();

        if ($pembayaran->isEmpty()) {
            abort(404);
        }

        // ambil data utama
        $first = $pembayaran->first();

        // hitung total
        $total = $pembayaran->sum('infaq_harga');

        $data = [
            'title' => 'Kwitansi ' . $id_transaksi,
            'id_transaksi' => $id_transaksi,
            'nama_siswa' => $first->nama_siswa,
            'nama_petugas' => $first->nama_petugas,
            'jenis_bayar' => $first->jenis_bayar,
            'tanggal' => $first->created_at,
            'total_bayar' => $total,
            'detail' => $pembayaran
        ];

        return view('rg-pembayaran-kwitansi', compact('data'));
    }

    public function pdfkwitansi($id_transaksi)
    {
        $pembayaran = Pembayaran::join('siswa', 'pembayaran.id_siswa', '=', 'siswa.id')
            ->join('guru', 'pembayaran.id_guru', '=', 'guru.id')
            ->join('jenisbayar', 'pembayaran.id_jenisbayar', '=', 'jenisbayar.id')
            ->where('pembayaran.id_transaksi', $id_transaksi)
            ->select(
                'pembayaran.*',
                'siswa.name as nama_siswa',
                'guru.name as nama_petugas',
                'jenisbayar.name as jenis_bayar'
            )
            ->get();

        if ($pembayaran->isEmpty()) {
            abort(404);
        }

        // ambil data utama
        $first = $pembayaran->first();

        // hitung total
        $total = $pembayaran->sum('infaq_harga');

        $data = [
            'title' => 'Kwitansi ' . $id_transaksi,
            'id_transaksi' => $id_transaksi,
            'nama_siswa' => $first->nama_siswa,
            'nama_petugas' => $first->nama_petugas,
            'jenis_bayar' => $first->jenis_bayar,
            'tanggal' => date('d-m-Y', strtotime($first->tanggal_pembayaran)),

            'total_bayar' => $total,
            'detail' => $pembayaran
        ];

        //return view('pdf.tagihan', compact('data', 'acara', 'kelas'));
        //$pdf = Pdf::loadView('pdf.tagihan', $data);
        $pdf = Pdf::loadView('pdf.kwitansi', compact('data'));

        //return $pdf->stream('tagihan.pdf');
        return $pdf->download('Kwitansi_' . $data['nama_siswa'] . '_' . $data['tanggal'] . '.pdf');
    }

}