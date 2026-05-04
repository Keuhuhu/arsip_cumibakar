<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Arsip - {{ $bulan ? 'Bulan ' . $bulan . ' ' : '' }}Tahun {{ $tahun }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0; text-transform: uppercase; }
        .header h2 { font-size: 14px; margin: 5px 0 0; font-weight: normal; }
        .title { text-align: center; margin-bottom: 20px; font-size: 14px; font-weight: bold; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 300px; float: right; }
        .signature p { margin-bottom: 60px; }
        
        @media print {
            body { padding: 0; }
            @page { margin: 1.5cm; size: landscape; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>PEMERINTAH KABUPATEN XYZ</h1>
        <h1>KECAMATAN ABC</h1>
        <h1>KANTOR KEPALA DESA CUMIBAKAR</h1>
        <h2>Jalan Raya Cumibakar No. 1, Kode Pos 12345</h2>
    </div>

    <div class="title">
        LAPORAN ARSIP DOKUMEN DESA<br>
        {{ $bulan ? 'BULAN ' . strtoupper(date('F', mktime(0, 0, 0, $bulan, 1))) . ' ' : '' }}TAHUN {{ $tahun }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">No. Urut</th>
                <th width="100">No. Surat</th>
                <th>Perihal</th>
                <th width="80">Tgl. Surat</th>
                <th>Asal/Tujuan</th>
                <th width="100">Kategori</th>
                <th width="80">Jenis</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dokumens as $i => $dok)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ str_pad($dok->no_urut, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $dok->no_surat ?: '-' }}</td>
                <td>{{ $dok->perihal }}</td>
                <td class="text-center">{{ $dok->tanggal_surat?->format('d/m/Y') }}</td>
                <td>{{ $dok->asal_surat ?? $dok->tujuan_surat ?? '-' }}</td>
                <td class="text-center">{{ $dok->kategori->nama ?? '-' }}</td>
                <td class="text-center">{{ $dok->jenis_label }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data arsip untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div></div>
        <div class="signature">
            <p>Cumibakar, {{ date('d F Y') }}<br>Kepala Desa Cumibakar</p>
            <p><strong>( ______________________ )</strong></p>
        </div>
    </div>

</body>
</html>
