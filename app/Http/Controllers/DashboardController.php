<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Agenda;
use App\Models\Dokumen;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistik umum
        $stats = [
            'total_dokumen'     => Dokumen::count(),
            'surat_masuk'       => Dokumen::where('jenis', 'masuk')->count(),
            'surat_keluar'      => Dokumen::where('jenis', 'keluar')->count(),
            'tindak_lanjut'     => Dokumen::where('status', Dokumen::STATUS_TINDAK_LANJUT)->count(),
            'total_bulan_ini'   => Dokumen::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)->count(),
            'agenda_masuk_bulan' => Agenda::where('jenis', 'masuk')
                                         ->whereMonth('tanggal_agenda', now()->month)->count(),
            'agenda_keluar_bulan'=> Agenda::where('jenis', 'keluar')
                                         ->whereMonth('tanggal_agenda', now()->month)->count(),
            'total_pengguna'    => User::where('is_active', true)->count(),
        ];

        // Dokumen terbaru
        $dokumenTerbaru = Dokumen::with(['kategori', 'uploader'])
                                  ->latest()
                                  ->take(10)
                                  ->get();

        // Statistik per kategori
        $perKategori = Kategori::withCount('dokumens')
                               ->active()
                               ->get();

        // Statistik bulanan (12 bulan terakhir)
        $bulanIni    = now();
        $chartLabels = [];
        $chartMasuk  = [];
        $chartKeluar = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = $bulanIni->copy()->subMonths($i);
            $chartLabels[] = $date->format('M Y');
            $chartMasuk[]  = Dokumen::where('jenis', 'masuk')
                                    ->whereYear('created_at', $date->year)
                                    ->whereMonth('created_at', $date->month)
                                    ->count();
            $chartKeluar[] = Dokumen::where('jenis', 'keluar')
                                    ->whereYear('created_at', $date->year)
                                    ->whereMonth('created_at', $date->month)
                                    ->count();
        }

        // Log aktivitas terbaru
        $logTerbaru = ActivityLog::with('user')->latest()->take(8)->get();

        // Agenda terbaru
        $agendaTerbaru = Agenda::with('creator')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'stats',
            'dokumenTerbaru',
            'perKategori',
            'chartLabels',
            'chartMasuk',
            'chartKeluar',
            'logTerbaru',
            'agendaTerbaru'
        ));
    }
}