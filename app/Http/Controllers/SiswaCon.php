<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Angkatan;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\Tahunajaran;
use App\Models\Infaq;
//use App\Models\Pengaturan;

class SiswaCon extends Controller
{


    public function index()
    {
        $data = [
            'title' => 'Kelola Data Siswa',
            'angkatan' => Angkatan::with('kelompok')
                ->orderBy('name', 'desc')
                ->get(),
        ];

        return view('rg-siswa', compact('data'));
    }

    public function byAngkatan($id)
    {
        //$angkatan = Angkatan::findOrFail($id);
        $angkatan = Angkatan::withCount('siswa')->find($id);

        $siswa = Siswa::where('id_angkatan', $id)
            ->orderBy('name')
            ->get();

        $data = [
            'title' => 'Data Siswa ' . optional($angkatan->kelompok)->name,
            'siswa' => $siswa,
            'angkatan' => $angkatan
        ];

        return view('rg-siswa-datasiswa', compact('data'));
    }


    public function angkatan_tambah()
    {
        $data = [
            'title' => 'Kenaikan Kelas & Tahun Ajaran Baru',
            'kelompok' => Kelompok::all(),
        ];

        return view('rg-siswa-angkatan-tambah', compact('data'));
    }

    public function angkatan_simpan(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'id_kelompok' => 'required'
        ]);

        Angkatan::create([
            'name' => $request->name,
            'id_kelompok' => $request->id_kelompok
        ]);

        return redirect('/datasiswa')->with('success', 'Angkatan berhasil disimpan');
    }

    public function naikkelas(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'tahunajaran_name' => 'required'
        ]);

        DB::beginTransaction();

        try {


            $ada = Angkatan::whereIn('id_kelompok', [2, 3, 4])->exists();

            if ($ada) {


                Angkatan::where('id_kelompok', 4)->update([
                    'id_kelompok' => 5,
                    'tahun_lulus' => now()
                ]);

                Angkatan::where('id_kelompok', 3)->update(['id_kelompok' => 4]);
                Angkatan::where('id_kelompok', 2)->update(['id_kelompok' => 3]);
            }


            Angkatan::create([
                'name' => $request->name,
                'id_kelompok' => 2
            ]);

            /*Tahunajaran::create([
                'name' => $request->tahunajaran_name,
            ]);*/
            $tahun = Tahunajaran::create([
                'name' => $request->tahunajaran_name,
            ]);

            $id_tahun = $tahun->id;

            $bulan = [
                '01 Infaq Juli',
                '02 Infaq Agustus',
                '03 Infaq September',
                '04 Infaq Oktober',
                '05 Infaq November',
                '06 Infaq Desember',
                '07 Infaq Januari',
                '08 Infaq Februari',
                '09 Infaq Maret',
                '10 Infaq April',
                '11 Infaq Mei',
                '12 Infaq Juni',
            ];

            $kelases = [2, 3, 4]; // 7,8,9

            foreach ($kelases as $kls) {

                $angkatan = Angkatan::where('id_kelompok', $kls)->first();
                if (!$angkatan)
                    continue;

                //  1. Infaq bulanan
                foreach ($bulan as $nama) {
                    Infaq::create([
                        'name' => $nama,
                        'id_tahunajaran' => $id_tahun,
                        'id_kelompok' => $kls,
                        'id_angkatan' => $angkatan->id,
                        'harga' => 50000
                    ]);
                }

                //  2. Umum semua kelas
                $umum = [
                    ['Pemeliharaan Sarpras', 250000],
                    ['Ekstrakurikuler', 150000],
                    ['ASTS 1', 50000],
                    ['ASAS 1', 50000],
                    ['ASAT', 50000],
                ];

                foreach ($umum as $u) {
                    Infaq::create([
                        'name' => $u[0],
                        'id_tahunajaran' => $id_tahun,
                        'id_kelompok' => $kls,
                        'id_angkatan' => $angkatan->id,
                        'harga' => $u[1]
                    ]);
                }

                //  3. Kelas 7 & 8
                if (in_array($kls, [2, 3])) {
                    Infaq::create([
                        'name' => 'ASTS 2',
                        'id_tahunajaran' => $id_tahun,
                        'id_kelompok' => $kls,
                        'id_angkatan' => $angkatan->id,
                        'harga' => 50000
                    ]);
                }

                //  4. Kelas 7 saja
                if ($kls == 2) {
                    $kelas7 = [
                        ['Atribut', 80000],
                        ['Sampul Raport', 50000],
                        ['Kaos Olahraga', 40000],
                    ];

                    foreach ($kelas7 as $k7) {
                        Infaq::create([
                            'name' => $k7[0],
                            'id_tahunajaran' => $id_tahun,
                            'id_kelompok' => $kls,
                            'id_angkatan' => $angkatan->id,
                            'harga' => $k7[1]
                        ]);
                    }
                }

                //  5. Kelas 8 saja
                if ($kls == 3) {
                    Infaq::create([
                        'name' => 'Asesmen Nasional',
                        'id_tahunajaran' => $id_tahun,
                        'id_kelompok' => $kls,
                        'id_angkatan' => $angkatan->id,
                        'harga' => 200000
                    ]);
                }

                //  6. Kelas 9 saja
                if ($kls == 4) {
                    Infaq::create([
                        'name' => 'ASAJ',
                        'id_tahunajaran' => $id_tahun,
                        'id_kelompok' => $kls,
                        'id_angkatan' => $angkatan->id,
                        'harga' => 600000
                    ]);
                }
            }

            DB::commit();

            return redirect('/datasiswa')->with('success', 'Kenaikan kelas dan Tahun Ajaran Baru berhasil dibuat');

        } catch (\Exception $e) {

            DB::rollBack();

            //return redirect()->back()->with('error', 'Gagal proses: ' . $e->getMessage());
            dd($e);
        }
    }

    public function datasiswa_tambah($id)
    {
        $angkatan = Angkatan::find($id);
        $data = [
            'title' => 'Tambah Siswa ' . optional($angkatan->kelompok)->name,
            'angkatan' => $angkatan
        ];

        return view('rg-siswa-datasiswa-tambah', compact('data'));
    }

    public function datasiswa_simpan(Request $request)
    {
        $names = $request->name;
        $whatsapps = $request->no_whatsapp;
        $id = $request->id_angkatan;

        if (empty(array_filter($names))) {
            return redirect()->route('siswa.angkatan', $id)
                ->with('error', 'Tidak ada data yang diisi');
        }

        for ($i = 0; $i < count($names); $i++) {

            $nama = trim($names[$i] ?? '');
            $wa = trim($whatsapps[$i] ?? '');

            if (($nama && !$wa) || (!$nama && $wa)) {
                return back()
                    ->withInput()
                    ->with('error', 'Nama dan No Whatsapp harus diisi bersamaan (cek baris ke-' . ($i + 1) . ')');
            }
        }

        $jumlah = 0;

        for ($i = 0; $i < count($names); $i++) {

            $nama = trim($names[$i] ?? '');
            $wa = trim($whatsapps[$i] ?? '');

            if (!$nama && !$wa)
                continue;

            Siswa::create([
                'name' => $nama,
                'no_whatsapp' => $wa,
                'id_angkatan' => $id
            ]);

            $jumlah++;
        }

        return redirect()->route('siswa.angkatan', $id)
            ->with('success', 'Berhasil menyimpan ' . $jumlah . ' data siswa');
    }

    public function hapus($id)
    {
        Siswa::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);

        $data = [
            'title' => 'Edit Siswa',
            'siswa' => $siswa
        ];

        return view('rg-siswa-datasiswa-edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'no_whatsapp' => 'required',
            //'role' => 'required'
        ]);

        $siswa = Siswa::findOrFail($id);
        $id = $request->id_angkatan;

        $dataUpdate = [
            'name' => $request->name,
            'no_whatsapp' => $request->no_whatsapp,
            //'role' => $request->role
        ];

        $siswa->update($dataUpdate);

        //return redirect('/dataguru')->with('success', 'Data berhasil diperbarui');
        return redirect()->route('siswa.angkatan', $id)->with('success', 'Data berhasil diperbarui');
    }

    public function spmb($id)
    {
        $angkatan = Angkatan::find($id);
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0'
        ])->get('https://spmb.smpmaarifnuwanareja.sch.id/apisiswa');  //masukkan url disini

        $data_api = [];

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status']) {
                $data_api = $result['data'];
            }
        }

        $data = [
            'title' => 'Ambil Data SPMB',
            'data_api' => $data_api,
            'angkatan' => $angkatan
        ];

        return view('rg-siswa-datasiswa-spmb', compact('data'));
    }
    public function apin($id)
    {
        $angkatan = Angkatan::find($id);
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0'
        ])->get('https://apin-1.smpmaarifnuwanareja.sch.id/apisiswa');  //masukkan url disini

        $data_api = [];

        if ($response->successful()) {
            $result = $response->json();

            if ($result['status']) {
                $data_api = $result['data'];
            }
        }

        $data = [
            'title' => 'Ambil Data Aplikasi Infaq Lama',
            'data_api' => $data_api,
            'angkatan' => $angkatan
        ];

        return view('rg-siswa-datasiswa-apin', compact('data'));
    }

    public function importSpmb(Request $request)
    {
        $selected = $request->siswa; // index yang dicentang
        $data = $request->data;
        $id = $request->id_angkatan;

        if (!$selected) {
            return back()->with('error', 'Pilih minimal 1 siswa');
        }

        $jumlah = 0;
        foreach ($selected as $i) {
            $s = $data[$i];

            Siswa::create([
                'name' => $s['name'],
                'no_whatsapp' => $s['no_whatsapp'],
                'id_angkatan' => $id
            ]);

            $jumlah++;
        }

        return redirect()->route('siswa.angkatan', $id)
            ->with('success', 'Berhasil menyimpan ' . $jumlah . ' data siswa');
    }
    public function importApin(Request $request)
    {
        $selected = $request->siswa; // index yang dicentang
        $data = $request->data;
        $id = $request->id_angkatan;

        if (!$selected) {
            return back()->with('error', 'Pilih minimal 1 siswa');
        }

        $jumlah = 0;
        foreach ($selected as $i) {
            $s = $data[$i];

            Siswa::create([
                'name' => $s['name'],
                'no_whatsapp' => $s['phonenumber'],
                'id_angkatan' => $id
            ]);

            $jumlah++;
        }

        return redirect()->route('siswa.angkatan', $id)
            ->with('success', 'Berhasil menyimpan ' . $jumlah . ' data siswa');
    }

}