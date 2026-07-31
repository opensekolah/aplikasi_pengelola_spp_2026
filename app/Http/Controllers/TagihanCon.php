<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Angkatan;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\Tahunajaran;
use App\Models\Infaq;
use App\Models\Acara;
use App\Models\Tagihan;
use App\Models\Pembayaran;
//use App\Models\Pengaturan;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanCon extends Controller
{


    public function index()
    {

        $data = [
            'title' => 'Tagihan',
            'acara' => DB::table('acara')
                ->join('guru', 'acara.id_guru', '=', 'guru.id')
                ->select('acara.*', 'guru.name as nama_guru')
                ->get(),
            /*->orderBy('name', 'desc')
            ->get(),*/
        ];

        return view('rg-tagihan', compact('data'));
    }

    public function byAcara($id)
    {
        //$angkatan = Angkatan::findOrFail($id);
        //$acara = Acara::withCount('siswa')->find($id);

        $acara = Acara::find($id);

        $kelaslist = DB::table('tagihan')
            ->join('kelompok', 'tagihan.id_kelompok', '=', 'kelompok.id')
            ->where('tagihan.id_acara', $acara->id)
            ->select('kelompok.id', 'kelompok.name')
            ->distinct()
            ->get();

        //dd($acara);
        $data = [
            'title' => 'Tagihan ' . $acara->name,
            //'siswa' => $siswa,
            'acara' => $acara,
            'kelaslist' => $kelaslist,
        ];

        return view('rg-tagihan-datatagihan', compact('data'));
    }


    public function tagihan_tambah()
    {
        // Ambil tahun ajaran terbaru
        $tahunajaran = Tahunajaran::orderBy('created_at', 'desc')->first();

        // Cegah jika belum ada data
        if (!$tahunajaran) {
            return back()->with('error', 'Belum ada tahun ajaran');
        }

        // Ambil infaq berdasarkan tahun ajaran terbaru
        $infaq = Infaq::where('id_tahunajaran', $tahunajaran->id)->get();
        //$infaq_grouped = $infaq->groupBy('id_kelompok');

        $data = [
            'title' => 'Buat Tagihan',
            'infaq_group' => $infaq->groupBy('id_kelompok'),
            'tahunajaran' => $tahunajaran
        ];

        return view('rg-tagihan-tambah', compact('data'));
    }

    public function tagihan_simpan(Request $request)
    {
        DB::beginTransaction();

        try {

            //dd($request->all());

            $acara_id = DB::table('acara')->insertGetId([
                'name' => $request->name,
                'created_at' => now(),
                'id_guru' => session('user_id'),
            ]);



            $infaqs = DB::table('infaq')
                ->join('tahunajaran', 'infaq.id_tahunajaran', '=', 'tahunajaran.id')
                ->whereIn('infaq.id', $request->id_infaq)
                ->select(
                    'infaq.*',
                    'tahunajaran.name as tahunajaran_name'
                )
                ->get();

            //dd($infaqs);


            foreach ($infaqs as $infaq) {
                //dd($infaq->id_angkatan);

                /*$angkatan = DB::table('angkatan')
                    ->where('id', $infaq->id_angkatan)
                    ->first();*/


                $siswas = DB::table('siswa')
                    ->where('id_angkatan', $infaq->id_angkatan)
                    ->get();
                // dd($siswas);
                if ($siswas->isEmpty()) {
                    continue;
                }

                foreach ($siswas as $siswa) {


                    DB::table('tagihan')->insert([
                        'id_acara' => $acara_id,
                        'id_siswa' => $siswa->id,
                        'id_kelompok' => $infaq->id_kelompok,

                        'tahunajaran_id' => $infaq->id_tahunajaran,
                        'tahunajaran_name' => $infaq->tahunajaran_name,

                        'infaq_id' => $infaq->id,
                        'infaq_name' => $infaq->name,
                        'infaq_harga' => $infaq->harga,

                        'created_at' => now()
                    ]);
                }
            }

            DB::commit();

            //$this->kirimBulkWhatsappTagihan($acara_id);
            $this->kirimBulkWhatsappTagihan(
                $acara_id,
                $request->id_kelompok
            );

            return redirect('/datatagihan')->with('success', 'Tagihan berhasil dibuat');

        } catch (\Exception $e) {

            DB::rollBack();

            //return redirect()->back()->with('error', $e->getMessage());
            dd($e);
        }
    }



    public function hapus($id_acara)
    {
        Acara::where('id', $id_acara)->delete();

        return redirect('/datatagihan')->with('success', 'Data berhasil dihapus');
    }


    public function oldpdf($acara, $kelas)
    {
        //dd($acara);
        $acaraname = Acara::find($acara);
        $kelasname = Kelompok::find($kelas);
        $tagihan = DB::table('tagihan')
            ->join('siswa', 'tagihan.id_siswa', '=', 'siswa.id')
            ->where('tagihan.id_acara', $acara)
            ->where('tagihan.id_kelompok', $kelas)
            ->select(
                'tagihan.*',
                'siswa.name as nama_siswa'
            )
            ->get()
            ->groupBy('id_kelompok')
            ->map(function ($kelasGroup) {
                return $kelasGroup->groupBy('id_siswa');
            });

        $data = [
            'title' => 'Tagihan',
            'tagihan' => $tagihan,
            'acaraname' => $acaraname->name,
            'kelasname' => $kelasname->name,
        ];

        //return view('pdf.tagihan', compact('data', 'acara', 'kelas'));
        //$pdf = Pdf::loadView('pdf.tagihan', $data);
        $pdf = Pdf::loadView('pdf.tagihan', compact('data', 'acara', 'kelas'));

        //return $pdf->stream('tagihan.pdf');
        return $pdf->download('Tagihan_' . $data['acaraname'] . '_' . $data['kelasname'] . '.pdf');
    }

    public function old2pdf($acara, $kelas)
    {
        $acaraname = Acara::findOrFail($acara);
        $kelasname = Kelompok::findOrFail($kelas);

        // =========================
        // 1. AMBIL TAGIHAN (MASTER)
        // =========================
        $tagihan = DB::table('tagihan')
            ->join('siswa', 'tagihan.id_siswa', '=', 'siswa.id')
            ->where('tagihan.id_acara', $acara)
            ->where('tagihan.id_kelompok', $kelas)
            ->select(
                'tagihan.id_siswa',
                'tagihan.infaq_id',
                'tagihan.infaq_harga',
                'tagihan.infaq_name',
                'siswa.name as nama_siswa'
            )
            ->get()
            ->groupBy('id_siswa');

        // =========================
        // 2. PEMBAYARAN
        // =========================
        $pembayaran = DB::table('pembayaran')
            ->select(
                'id_siswa',
                'infaq_id',
                DB::raw('SUM(infaq_harga) as total')
            )
            ->groupBy('id_siswa', 'infaq_id')
            ->get()
            ->groupBy('id_siswa');

        // =========================
        // 3. HITUNG SISA + FILTER
        // =========================
        $result = $tagihan->map(function ($items, $id_siswa) use ($pembayaran) {

            $nama_siswa = $items[0]->nama_siswa;

            $data = $items->map(function ($t) use ($pembayaran, $id_siswa) {

                $bayar = $pembayaran[$id_siswa] ?? collect();
                $itemBayar = $bayar->firstWhere('infaq_id', $t->infaq_id);

                $sisa = $t->infaq_harga - ($itemBayar->total ?? 0);

                // skip kalau lunas
                if ($sisa <= 0)
                    return null;

                return [
                    'infaq_name' => $t->infaq_name,
                    'sisa' => $sisa
                ];
            })->filter()->values();

            // kalau semua lunas → skip siswa
            if ($data->isEmpty())
                return null;

            return [
                'nama_siswa' => $nama_siswa,
                'items' => $data
            ];
        })
            ->filter()
            ->values();

        $data = [
            'title' => 'Tagihan',
            'tagihan' => $result,
            'acaraname' => $acaraname->name,
            'kelasname' => $kelasname->name,
        ];

        //$pdf = Pdf::loadView('pdf.tagihan', compact('data', 'acara', 'kelas'));
        $pdf = Pdf::loadView('pdf.tagihan', compact('data', 'acara', 'kelas'))
            ->setPaper('a4', 'landscape');

        return $pdf->download(
            'Tagihan_' . $data['acaraname'] . '_' . $data['kelasname'] . '.pdf'
        );
    }

    public function pdf($acara, $kelas)
    {
        $acaraname = Acara::findOrFail($acara);
        $kelasname = Kelompok::findOrFail($kelas);

        // =========================
        // 1. TAGIHAN
        // =========================
        $tagihan = DB::table('tagihan')
            ->join('siswa', 'tagihan.id_siswa', '=', 'siswa.id')
            ->where('tagihan.id_acara', $acara)
            ->where('tagihan.id_kelompok', $kelas)
            ->select(
                'tagihan.id_siswa',
                'tagihan.infaq_id',
                'tagihan.infaq_harga',
                'tagihan.infaq_name',
                'siswa.name as nama_siswa'
            )
            ->get()
            ->groupBy('id_siswa');

        // =========================
        // 2. PEMBAYARAN
        // =========================
        $pembayaran = DB::table('pembayaran')
            ->select(
                'id_siswa',
                'infaq_id',
                DB::raw('SUM(infaq_harga) as total')
            )
            ->groupBy('id_siswa', 'infaq_id')
            ->get()
            ->groupBy('id_siswa');

        // =========================
        // 3. FORMAT HASIL
        // =========================
        $result = $tagihan->map(function ($items, $id_siswa) use ($pembayaran) {

            $nama_siswa = $items[0]->nama_siswa;
            $bayarSiswa = $pembayaran[$id_siswa] ?? collect();

            $total = 0;

            $data = $items->map(function ($t) use ($bayarSiswa, &$total) {

                $itemBayar = $bayarSiswa->firstWhere('infaq_id', $t->infaq_id);

                $sisa = $t->infaq_harga - ($itemBayar->total ?? 0);

                // kalau masih ada sisa, masukin
                if ($sisa > 0) {
                    $total += $sisa;

                    return [
                        'infaq_name' => $t->infaq_name,
                        'sisa' => $sisa
                    ];
                }

                return null;

            })->filter()->values();

            // =========================
            // 4. JANGAN SKIP SISWA
            // =========================
            return [
                'nama_siswa' => $nama_siswa,
                'items' => $data,
                'total' => $total // 🔥 ini penting
            ];
        })->values();

        // =========================
        // 5. DATA VIEW
        // =========================
        $data = [
            'title' => 'Tagihan',
            'tagihan' => $result,
            'acaraname' => $acaraname->name,
            'kelasname' => $kelasname->name,
        ];

        $pdf = Pdf::loadView('pdf.tagihan', compact('data', 'acara', 'kelas'))
            ->setPaper('a4', 'landscape');

        return $pdf->download(
            'Tagihan_' . $data['acaraname'] . '_' . $data['kelasname'] . '.pdf'
        );
    }

    private function kirimBulkWhatsappTagihan($acara, $kelas)
    {
        try {

            // =========================
            // 1. AMBIL TAGIHAN
            // =========================
            $tagihan = DB::table('tagihan')
                ->join('siswa', 'tagihan.id_siswa', '=', 'siswa.id')
                ->where('tagihan.id_acara', $acara)
                ->where('tagihan.id_kelompok', $kelas)
                ->select(
                    'tagihan.id_siswa',
                    'tagihan.infaq_id',
                    'tagihan.infaq_harga',
                    'tagihan.infaq_name',
                    'siswa.name as nama_siswa',
                    'siswa.no_whatsapp'
                )
                ->get()
                ->groupBy('id_siswa');


            // =========================
            // 2. AMBIL PEMBAYARAN
            // =========================
            $pembayaran = DB::table('pembayaran')
                ->select(
                    'id_siswa',
                    'infaq_id',
                    DB::raw('SUM(infaq_harga) as total')
                )
                ->groupBy(
                    'id_siswa',
                    'infaq_id'
                )
                ->get()
                ->groupBy('id_siswa');



            $data = [];



            // =========================
            // 3. FORMAT DATA WHATSAPP
            // =========================
            foreach ($tagihan as $id_siswa => $items) {


                $siswa = $items->first();


                $bayarSiswa = $pembayaran[$id_siswa] ?? collect();



                $rincian = "";

                $total = 0;

                $nomor = 1;



                foreach ($items as $item) {


                    $itemBayar = $bayarSiswa
                        ->firstWhere(
                            'infaq_id',
                            $item->infaq_id
                        );



                    $sisa =
                        $item->infaq_harga -
                        ($itemBayar->total ?? 0);



                    // hanya tampilkan yang belum lunas
                    if ($sisa > 0) {


                        $rincian .=
                            "- " .
                            $item->infaq_name .
                            "= Rp " .
                            number_format(
                                $sisa,
                                0,
                                ',',
                                '.'
                            ) .
                            "\n";


                        $total += $sisa;

                        $nomor++;

                    }

                }



                // =========================
                // NORMALISASI NOMOR WA
                // =========================
                $nomorWhatsapp = $siswa->no_whatsapp;


                if (
                    str_starts_with(
                        $nomorWhatsapp,
                        '0'
                    )
                ) {

                    $nomorWhatsapp =
                        '62' .
                        substr(
                            $nomorWhatsapp,
                            1
                        );

                }



                // =========================
                // MASUK DATA BULK
                // =========================
                if (
                    $total > 0 &&
                    $nomorWhatsapp
                ) {


                    $data[] = [

                        "phone" => $nomorWhatsapp,

                        "name" => $siswa->nama_siswa,

                        "rincian" => $rincian,

                        "total" => number_format(
                            $total,
                            0,
                            ',',
                            '.'
                        )

                    ];

                }

            }


            //dd($data);

            // tidak ada tagihan
            if (count($data) == 0) {
                return;
            }



            // =========================
            // 4. KIRIM KE WA GATEWAY
            // =========================
            $response = Http::post(
                config('services.whatsapp.gateway') . '/send/bulk',
                [

                    "api_key" =>
                        config('services.whatsapp.api_key'),


                    "template" =>
                        "Assalamualaikum wr. wb.😊🙏🏼

Yth Org Tua/Wali dari *{{name}}*

Berikut ini kami kirimkan rincian administrasi SMP Ma'arif NU 01 Wanareja. Apabila ada kesalahan bisa datang langsung ke SMP menemui Bu Ining Suryani, S.Ag.
Pembayaran bisa dicicil. Melayani juga pembayaran lewat Transfer Bank apabila sedang di luar kota.

Rincian Administrasi *{{name}}* :

{{rincian}}

Jumlah:
Rp{{total}}

Pesan ini dikirim secara otomatis, jadi tidak bisa membalas Anda.
Apabila ada pertanyaan bisa WA ke nomor bu *Ining Suryani* : 0881010822346

Terima kasih.😊🙏🏼",


                    "data" => $data

                ]
            );



            Log::info(
                'Bulk WA Tagihan',
                [
                    'total' => count($data),
                    'response' => $response->json()
                ]
            );


            return $response->json();



        } catch (\Exception $e) {


            Log::error(
                'Gagal kirim bulk WA tagihan: ' .
                $e->getMessage()
            );


            return null;

        }
    }

}