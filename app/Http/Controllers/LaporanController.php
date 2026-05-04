<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Dokumen;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $tahun  = $request->get('tahun', now()->year);
        $bulan  = $request->get('bulan', null);
        $jenis  = $request->get('jenis', null);

        $query = Dokumen::with(['kategori', 'uploader'])->whereYear('created_at', $tahun);
        if ($bulan) $query->whereMonth('created_at', $bulan);
        if ($jenis) $query->where('jenis', $jenis);

        $dokumens  = $query->latest()->paginate(20)->withQueryString();
        $kategoris = Kategori::withCount('dokumens')->active()->get();

        // Statistik tahunan per bulan
        $bulananData = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulananData[] = [
                'bulan'  => \Carbon\Carbon::create()->month($m)->format('M'),
                'masuk'  => Dokumen::whereYear('created_at', $tahun)->whereMonth('created_at', $m)->where('jenis', 'masuk')->count(),
                'keluar' => Dokumen::whereYear('created_at', $tahun)->whereMonth('created_at', $m)->where('jenis', 'keluar')->count(),
            ];
        }

        return view('laporan.index', compact('dokumens', 'kategoris', 'bulananData', 'tahun', 'bulan', 'jenis'));
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $bulan = $request->get('bulan');

        $query = Dokumen::with(['kategori', 'uploader'])->whereYear('created_at', $tahun);
        if ($bulan) $query->whereMonth('created_at', $bulan);
        $dokumens = $query->get();

        ActivityLog::log('export', "Ekspor data arsip ke Excel (tahun: {$tahun})");

        // Generate CSV (without Excel package for simplicity, can be upgraded)
        $filename = 'Rekap_Arsip_' . ($bulan ? \Carbon\Carbon::create()->month($bulan)->format('F_') : '') . $tahun . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($dokumens) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['No', 'No. Urut', 'No. Surat', 'Perihal', 'Tanggal Surat', 'Asal/Tujuan', 'Kategori', 'Jenis', 'Status', 'Diunggah Oleh', 'Tanggal Unggah']);

            foreach ($dokumens as $i => $dok) {
                fputcsv($file, [
                    $i + 1,
                    $dok->no_urut,
                    $dok->no_surat ?? '-',
                    $dok->perihal,
                    $dok->tanggal_surat?->format('d/m/Y'),
                    $dok->asal_surat ?? $dok->tujuan_surat ?? '-',
                    $dok->kategori?->nama ?? '-',
                    $dok->jenis_label,
                    $dok->status_label,
                    $dok->uploader?->name ?? '-',
                    $dok->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request): View
    {
        $tahun    = $request->get('tahun', now()->year);
        $bulan    = $request->get('bulan');
        $query    = Dokumen::with(['kategori', 'uploader'])->whereYear('created_at', $tahun);
        if ($bulan) $query->whereMonth('created_at', $bulan);
        $dokumens = $query->get();

        ActivityLog::log('export', "Ekspor data arsip ke PDF (tahun: {$tahun})");

        return view('laporan.pdf', compact('dokumens', 'tahun', 'bulan'));
    }
}