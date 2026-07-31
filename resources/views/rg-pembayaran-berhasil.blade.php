@include('header')
@include('ruangguru')
<div class="content-area">
    <!-- start content-area -->

    <div class="d-flex justify-content-center py-4">
        <div class="card shadow-sm" id="print-area"
            style="
    width: 100%;
    max-width: 390px;
    background-image: url('{{ asset('uploads/bg-transaksi.png') }}');
    background-size: cover;
    background-position: center;
">
            <div class="card-body">
                <h4 class="text-center mb-1">Transaksi Berhasil</h4>
                <p class="text-center text-muted small mb-3">{{ $tanggal_transaksi }}</p>

                <div class="text-center mb-3">
                    <p class="mb-0 text-muted">Total Transaksi</p>
                    <h3 class="fw-bold mb-0">Rp{{ number_format($total_transaksi, 0, ',', '.') }}</h3>
                    <p class="text-muted small">No. Ref: {{ $id_transaksi }}</p>
                </div>

                <hr>

                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td class="text-end">{{ $nama_siswa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Pembayaran</td>
                            <td class="text-end">{{ $jenis_bayar_label ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Whatsapp</td>
                            <td class="text-end">{{ $no_whatsapp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Petugas</td>
                            <td class="text-end">{{ $nama_petugas ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <p class="text-muted mb-2">Untuk Pembayaran</p>
                <table class="table table-sm">
                    <tbody>
                        @foreach ($pembayaran as $p)
                            <tr>
                                <td>{{ $p->infaq_name }}</td>
                                <td class="text-end">{{ number_format($p->infaq_harga, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 p-3 pt-0 no-print">
                <button type="button" class="btn btn-outline-primary w-50" onclick="window.print()"><i data-lucide="printer"></i> Cetak</button>
                <a href="{{ url('/datapembayaran') }}" class="btn btn-primary w-50 text-decoration-none"><i data-lucide="check-circle"></i> Selesai</a>
            </div>
        </div>
    </div>

</div> <!-- end content-area -->
</div> <!-- end content -->
</div> <!-- end b -->
</div> <!-- end layoutadmin -->

<style>
    @media print {

        /* sembunyikan semua elemen di body */
        body * {
            visibility: hidden;
        }

        @page {
            size: 7cm auto;
            margin: 1cm;
        }

        /* tampilkan hanya print-area dan semua isinya */
        #print-area,
        #print-area * {
            visibility: visible;
        }

        /* posisikan print-area di pojok kiri atas halaman cetak */
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 7cm !important;
            max-width: 7cm !important;
            box-shadow: none;
            border: 1px dashed #000 !important;
            /* garis potong */
            box-sizing: border-box;
        }

        /* sembunyikan tombol cetak & selesai saat print */
        .no-print {
            display: none !important;
        }

        #print-area .table,
        #print-area .table td,
        #print-area .table tr,
        #print-area .card-body {
            background-color: transparent !important;
        }

        #print-area,
        .card {
            font-family: 'Times New Roman', serif;
        }

        #print-area h3,
        #print-area h4 {
            font-size: 10pt !important;
            margin-bottom: 0 !important;
        }

        #print-area p {
            font-size: 10pt !important;
            margin-bottom: 0 !important;
        }

        #print-area .table td {
            font-size: 10pt !important;
            padding: 0 !important;
        }

        #print-area .text-muted {
            font-size: 10pt !important;
        }
    }
</style>

@include('footer')
