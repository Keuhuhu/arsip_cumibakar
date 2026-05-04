<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Agenda;
use App\Models\Dokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Agenda::with(['creator', 'dokumen']);

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('perihal', 'like', "%{$request->q}%")
                  ->orWhere('no_agenda', 'like', "%{$request->q}%")
                  ->orWhere('asal', 'like', "%{$request->q}%")
                  ->orWhere('no_surat', 'like', "%{$request->q}%");
            });
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal_agenda', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_agenda', '<=', $request->sampai);
        }

        $agendas = $query->latest()->paginate(15)->withQueryString();

        // Counter bulan ini
        $masukBulanIni  = Agenda::where('jenis', 'masuk')->whereMonth('tanggal_agenda', now()->month)->count();
        $keluarBulanIni = Agenda::where('jenis', 'keluar')->whereMonth('tanggal_agenda', now()->month)->count();

        return view('agenda.index', compact('agendas', 'masukBulanIni', 'keluarBulanIni'));
    }

    public function create(): View
    {
        $dokumens = Dokumen::orderBy('created_at', 'desc')->take(50)->get();
        return view('agenda.create', compact('dokumens'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis'          => 'required|in:masuk,keluar',
            'no_surat'       => 'nullable|string|max:100',
            'tanggal_surat'  => 'required|date',
            'tanggal_agenda' => 'required|date',
            'perihal'        => 'required|string|max:500',
            'asal'           => 'nullable|string|max:255',
            'tujuan'         => 'nullable|string|max:255',
            'keterangan'     => 'nullable|string',
            'dokumen_id'     => 'nullable|exists:dokumens,id',
        ]);

        // Auto-generate nomor agenda
        $noAgenda = Agenda::generateNoAgenda($validated['jenis'], now()->year);

        $agenda = Agenda::create([
            ...$validated,
            'no_agenda'  => $noAgenda,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::log('create', "Mencatat agenda: {$agenda->no_agenda} - {$agenda->perihal}", $agenda);

        return redirect()->route('agenda.show', $agenda)
                         ->with('success', "Agenda berhasil dicatat dengan nomor {$noAgenda}.");
    }

    public function show(Agenda $agenda): View
    {
        $agenda->load(['creator', 'dokumen']);
        return view('agenda.show', compact('agenda'));
    }

    public function edit(Agenda $agenda): View
    {
        $dokumens = Dokumen::orderBy('created_at', 'desc')->take(50)->get();
        return view('agenda.edit', compact('agenda', 'dokumens'));
    }

    public function update(Request $request, Agenda $agenda): RedirectResponse
    {
        $validated = $request->validate([
            'jenis'          => 'required|in:masuk,keluar',
            'no_surat'       => 'nullable|string|max:100',
            'tanggal_surat'  => 'required|date',
            'tanggal_agenda' => 'required|date',
            'perihal'        => 'required|string|max:500',
            'asal'           => 'nullable|string|max:255',
            'tujuan'         => 'nullable|string|max:255',
            'keterangan'     => 'nullable|string',
            'dokumen_id'     => 'nullable|exists:dokumens,id',
        ]);

        $agenda->update($validated);
        ActivityLog::log('edit', "Mengedit agenda: {$agenda->no_agenda}", $agenda);

        return redirect()->route('agenda.show', $agenda)
                         ->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        $noAgenda = $agenda->no_agenda;
        $agenda->delete();
        ActivityLog::log('delete', "Menghapus agenda: {$noAgenda}");

        return redirect()->route('agenda.index')
                         ->with('success', 'Agenda berhasil dihapus.');
    }
}