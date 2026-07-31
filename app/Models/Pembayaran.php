<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'id_transaksi',
        'tanggal_pembayaran',
        'id_angkatan',
        'id_siswa',
        'infaq_id',
        'infaq_name',
        'infaq_harga',
        'id_guru',
        'id_jenisbayar'
    ];

    public $timestamps = false;

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function jenisbayar()
    {
        return $this->belongsTo(Jenisbayar::class, 'id_jenisbayar');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}