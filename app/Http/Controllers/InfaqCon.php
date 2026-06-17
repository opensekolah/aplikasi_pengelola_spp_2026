<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Angkatan;
use App\Models\Tahunajaran;
use App\Models\Infaq;
use App\Models\Siswa;
use App\Models\Kelompok;
//use App\Models\Pengaturan;

class InfaqCon extends Controller
{


    public function oldindex()
    {
        $data = [
            'title' => 'Kelola Data Infaq',
            'tahunajaran' => Tahunajaran::all(),
            /*with('kelompok')
                ->orderBy('name', 'desc')
                ->get(),*/
        ];

        return view('rg-infaq', compact('data'));
    }
    public function index(Request $request)
    {
        // ambil tahun ajaran terbaru
        $tahunajaran = Tahunajaran::orderBy('created_at', 'desc')->first();

        // query dasar
        $query = Infaq::where('id_tahunajaran', $tahunajaran->id);

        // filter kelas dari URL (?kelas=)
        if ($request->kelas) {
            $query->where('id_kelompok', $request->kelas);
        }

        // ambil data + relasi
        $infaq = $query->with('kelompok')->get();

        $data = [
            'title' => 'Data Infaq Tahun Ajaran ' . $tahunajaran->name,
            'infaq' => $infaq,
            'tahunajaran' => $tahunajaran,
            'filter_kelas' => $request->kelas
        ];

        return view('rg-infaq-datainfaq', compact('data'));
    }


    public function byTahunajaran($id, Request $request)
    {
        $tahunajaran = Tahunajaran::findOrFail($id);

        //  query dasar
        $query = Infaq::where('id_tahunajaran', $id);

        //  filter kelas
        if ($request->kelas) {
            $query->where('id_kelompok', $request->kelas);
        }

        //  ambil data + relasi (biar bisa tampil nama kelas)
        $infaq = $query->with('kelompok')->get();

        $data = [
            'title' => 'Data Infaq Tahun Ajaran ' . $tahunajaran->name,
            'infaq' => $infaq,
            'tahunajaran' => $tahunajaran,
            'filter_kelas' => $request->kelas // biar kepake di view
        ];

        return view('rg-infaq-datainfaq', compact('data'));
    }

    public function datainfaq_tambah($id)
    {
        $tahunajaran = Tahunajaran::find($id);
        $data = [
            'title' => 'Tambah Infaq Tahun Ajaran ' . $tahunajaran->name,
            'tahunajaran' => $tahunajaran,
            'kelompok' => Kelompok::all()
        ];

        return view('rg-infaq-datainfaq-tambah', compact('data'));
    }

    public function datainfaq_simpan(Request $request)
    {
        $names = $request->name;
        $kelompok = $request->id_kelompok;
        $hargas = $request->harga;
        $tahunId = $request->id_tahunajaran;


        if (empty(array_filter($names))) {
            return back()
                ->withInput()
                ->with('error', 'Tidak ada data yang diisi');
        }

        for ($i = 0; $i < count($names); $i++) {

            $nama = trim($names[$i] ?? '');
            $kls = $kelompok[$i] ?? '';
            $nom = $hargas[$i] ?? '';

            // kalau salah satu isi → semua wajib
            if ($nama || $kls || $nom) {
                if (!$nama || !$kls || !$nom) {
                    return back()
                        ->withInput()
                        ->with('error', 'Semua field harus diisi (baris ke-' . ($i + 1) . ')');
                }
            }
        }

        for ($i = 0; $i < count($names); $i++) {

            $nama = trim($names[$i] ?? '');
            $kls = $kelompok[$i] ?? '';
            $nom = $hargas[$i] ?? '';

            // skip kosong total
            if (!$nama && !$kls && !$nom)
                continue;

            $angkatan = Angkatan::where('id_kelompok', $kls)->first();

            if (!$angkatan)
                continue;

            Infaq::create([
                'name' => $nama,
                'id_tahunajaran' => $tahunId,
                'id_kelompok' => $kls,
                'id_angkatan' => $angkatan->id,
                'harga' => $nom
            ]);
        }


        return redirect()->route('infaq.tahunajaran', $tahunId)
            ->with('success', 'Berhasil menyimpan data infaq');
    }

    public function hapus($id)
    {
        Infaq::where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $infaq = Infaq::findOrFail($id);

        $data = [
            'title' => 'Edit Infaq',
            'infaq' => $infaq,
            'kelompok' => Kelompok::whereIn('id', [2, 3, 4])->get(),
        ];

        return view('rg-infaq-datainfaq-edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'harga' => 'required|numeric',
            'id_kelompok' => 'required'
        ]);

        $infaq = Infaq::findOrFail($id);
        $id = $request->id_tahunajaran;

        //  cari ulang angkatan sesuai kelas
        $angkatan = Angkatan::where('id_kelompok', $request->id_kelompok)->first();

        $infaq->update([
            'name' => $request->name,
            'harga' => $request->harga,
            'id_kelompok' => $request->id_kelompok,
            'id_angkatan' => $angkatan->id ?? null,
            'id_tahunajaran' => $request->id_tahunajaran
        ]);

        //return redirect('/dataguru')->with('success', 'Data berhasil diperbarui');
        return redirect()->route('infaq.tahunajaran', $id)->with('success', 'Data berhasil diperbarui');
    }

}