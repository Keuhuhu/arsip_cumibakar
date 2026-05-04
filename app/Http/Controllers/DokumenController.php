<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Dokumen;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DokumenController extends Controller
{
    public function index(Request $request): View
    {
        $query = Dokumen::with(['kategori', 'uploader']);

        // Search & Filter
        if ($request->filled('q')) {
            $query->search($request->q);
        }
        if ($request->filled('kategori_id')) {
            $query->byKategori($request->kategori_id);
        }
        if ($request->filled('jenis')) {
            $query->byJenis($request->jenis);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('dari') || $request->filled('sampai')) {
            $query->byDateRange($request->dari, $request->sampai);
        }

        $dokumens   = $query->latest()->paginate(15)->withQueryString();
        $kategoris  = Kategori::active()->get();

        return view('dokumen.index', compact('dokumens', 'kategoris'));
    }

    public function create(): View
    {
        $kategoris = Kategori::active()->get();
        return view('dokumen.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_surat'      => 'nullable|string|max:100',
            'perihal'       => 'required|string|max:500',
            'tanggal_surat' => 'required|date',
            'tanggal_masuk' => 'nullable|date',
            'asal_surat'    => 'nullable|string|max:255',
            'tujuan_surat'  => 'nullable|string|max:255',
            'kategori_id'   => 'required|exists:kategoris,id',
            'jenis'         => 'required|in:masuk,keluar,sk,laporan,kependudukan',
            'keterangan'    => 'nullable|string',
            'file'          => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'tags'          => 'nullable|string',
        ], [
            'perihal.required'   => 'Perihal wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'jenis.required'     => 'Jenis dokumen wajib dipilih.',
            'file.required'      => 'File dokumen wajib diunggah.',
            'file.max'           => 'Ukuran file maksimal 10MB.',
            'file.mimes'         => 'Format file harus PDF, Word, Excel, atau gambar (JPG/PNG).',
        ]);

        // Upload file
        $file = $request->file('file');
        $fileName  = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $filePath  = $file->storeAs('dokumen/' . now()->format('Y/m'), $fileName, 's3');

        // Generate no_urut
        $noUrut = Dokumen::whereYear('created_at', now()->year)->max('no_urut') + 1;

        // Tags
        $tags = $request->filled('tags')
            ? array_map('trim', explode(',', $request->tags))
            : [];

        $dokumen = Dokumen::create([
            ...$validated,
            'no_urut'     => $noUrut,
            'file_path'   => $filePath,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'file_type'   => $file->getClientMimeType(),
            'uploaded_by' => auth()->id(),
            'status'      => Dokumen::STATUS_AKTIF,
            'tags'        => $tags,
        ]);

        ActivityLog::log('upload', "Mengunggah dokumen: {$dokumen->perihal}", $dokumen, [
            'no_surat' => $dokumen->no_surat,
            'jenis'    => $dokumen->jenis,
        ]);

        return redirect()->route('dokumen.show', $dokumen)
                         ->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(Dokumen $dokumen): View
    {
        $dokumen->load(['kategori', 'uploader', 'approver']);
        return view('dokumen.show', compact('dokumen'));
    }

    public function edit(Dokumen $dokumen): View
    {
        $kategoris = Kategori::active()->get();
        return view('dokumen.edit', compact('dokumen', 'kategoris'));
    }

    public function update(Request $request, Dokumen $dokumen): RedirectResponse
    {
        $validated = $request->validate([
            'no_surat'      => 'nullable|string|max:100',
            'perihal'       => 'required|string|max:500',
            'tanggal_surat' => 'required|date',
            'tanggal_masuk' => 'nullable|date',
            'asal_surat'    => 'nullable|string|max:255',
            'tujuan_surat'  => 'nullable|string|max:255',
            'kategori_id'   => 'required|exists:kategoris,id',
            'jenis'         => 'required|in:masuk,keluar,sk,laporan,kependudukan',
            'keterangan'    => 'nullable|string',
            'file'          => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'tags'          => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file from S3
            Storage::disk('s3')->delete($dokumen->file_path);

            $file = $request->file('file');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('dokumen/' . now()->format('Y/m'), $fileName, 's3');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['file_type'] = $file->getClientMimeType();
        }

        $tags = $request->filled('tags')
            ? array_map('trim', explode(',', $request->tags))
            : [];
        $validated['tags'] = $tags;

        $dokumen->update($validated);

        ActivityLog::log('edit', "Mengedit dokumen: {$dokumen->perihal}", $dokumen);

        return redirect()->route('dokumen.show', $dokumen)
                         ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Dokumen $dokumen): RedirectResponse
    {
        $perihal = $dokumen->perihal;
        Storage::disk('s3')->delete($dokumen->file_path);
        $dokumen->delete();

        ActivityLog::log('delete', "Menghapus dokumen: {$perihal}");

        return redirect()->route('dokumen.index')
                         ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Dokumen $dokumen)
    {
        ActivityLog::log('download', "Mengunduh dokumen: {$dokumen->perihal}", $dokumen);

        if (!Storage::disk('s3')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('s3')->download($dokumen->file_path, $dokumen->file_name);
    }

    public function preview(Dokumen $dokumen)
    {
        if (!Storage::disk('s3')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $content  = Storage::disk('s3')->get($dokumen->file_path);
        $mimeType = $dokumen->file_type ?? 'application/octet-stream';

        return response($content, 200)->header('Content-Type', $mimeType);
    }

    public function updateStatus(Request $request, Dokumen $dokumen): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:aktif,tindak_lanjut,selesai,disetujui',
        ]);

        $oldStatus = $dokumen->status;
        $dokumen->update([
            'status'      => $validated['status'],
            'approved_by' => $validated['status'] === 'disetujui' ? auth()->id() : $dokumen->approved_by,
            'approved_at' => $validated['status'] === 'disetujui' ? now() : $dokumen->approved_at,
        ]);

        ActivityLog::log('approve', "Mengubah status dokumen: {$dokumen->perihal} dari {$oldStatus} ke {$validated['status']}", $dokumen);

        return back()->with('success', 'Status dokumen berhasil diperbarui.');
    }
}