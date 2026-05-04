<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        $kategoris = Kategori::withCount('dokumens')->orderBy('urutan')->paginate(15);
        return view('kategori.index', compact('kategoris'));
    }

    public function create(): View
    {
        return view('kategori.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:kategoris,nama',
            'kode'      => 'required|string|max:10|unique:kategoris,kode',
            'deskripsi' => 'nullable|string',
            'warna'     => 'nullable|string|max:7',
            'icon'      => 'nullable|string|max:10',
            'urutan'    => 'nullable|integer|min:1',
        ]);

        $validated['is_active'] = true;
        $validated['urutan']    = $validated['urutan'] ?? (Kategori::max('urutan') + 1);

        $kategori = Kategori::create($validated);
        ActivityLog::log('create', "Menambah kategori: {$kategori->nama}");

        return redirect()->route('kategori.index')
                         ->with('success', "Kategori {$kategori->nama} berhasil ditambahkan.");
    }

    public function show(Kategori $kategori): View
    {
        $kategori->load('dokumens');
        return view('kategori.show', compact('kategori'));
    }

    public function edit(Kategori $kategori): View
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => "required|string|max:100|unique:kategoris,nama,{$kategori->id}",
            'kode'      => "required|string|max:10|unique:kategoris,kode,{$kategori->id}",
            'deskripsi' => 'nullable|string',
            'warna'     => 'nullable|string|max:7',
            'icon'      => 'nullable|string|max:10',
            'urutan'    => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $kategori->update($validated);
        ActivityLog::log('edit', "Mengedit kategori: {$kategori->nama}");

        return redirect()->route('kategori.index')
                         ->with('success', "Kategori {$kategori->nama} berhasil diperbarui.");
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        if ($kategori->dokumens()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki dokumen.');
        }

        $nama = $kategori->nama;
        $kategori->delete();
        ActivityLog::log('delete', "Menghapus kategori: {$nama}");

        return redirect()->route('kategori.index')
                         ->with('success', "Kategori {$nama} berhasil dihapus.");
    }
}