<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    private string $backupDisk = 'local';
    private string $backupPath = 'backups';

    public function index(): View
    {
        $files = [];
        if (Storage::disk($this->backupDisk)->exists($this->backupPath)) {
            $rawFiles = Storage::disk($this->backupDisk)->files($this->backupPath);
            foreach ($rawFiles as $file) {
                $files[] = [
                    'name'       => basename($file),
                    'path'       => $file,
                    'size'       => Storage::disk($this->backupDisk)->size($file),
                    'modified'   => Storage::disk($this->backupDisk)->lastModified($file),
                ];
            }
            // Sort by newest
            usort($files, fn($a, $b) => $b['modified'] <=> $a['modified']);
        }

        return view('backup.index', compact('files'));
    }

    public function create(Request $request): RedirectResponse
    {
        try {
            // Create backup directory
            Storage::disk($this->backupDisk)->makeDirectory($this->backupPath);

            $filename = 'backup_arsip_cumibakar_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $backupFile = $this->backupPath . '/' . $filename;

            // Export SQLite database (simple file copy for SQLite)
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                Storage::disk($this->backupDisk)->put($backupFile, file_get_contents($dbPath));

                ActivityLog::log('backup', "Membuat backup database: {$filename}");

                return back()->with('success', "Backup berhasil dibuat: {$filename}");
            }

            // For MySQL: use mysqldump
            $dbConfig = config('database.connections.mysql');
            if ($dbConfig) {
                $host     = $dbConfig['host'];
                $port     = $dbConfig['port'];
                $database = $dbConfig['database'];
                $username = $dbConfig['username'];
                $password = $dbConfig['password'];

                $command = "mysqldump --host={$host} --port={$port} --user={$username} --password={$password} {$database} 2>/dev/null";
                $output  = shell_exec($command);

                if ($output) {
                    Storage::disk($this->backupDisk)->put($backupFile, $output);
                    ActivityLog::log('backup', "Membuat backup database: {$filename}");
                    return back()->with('success', "Backup berhasil dibuat: {$filename}");
                }
            }

            return back()->with('error', 'Gagal membuat backup. Pastikan konfigurasi database benar.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat backup: ' . $e->getMessage());
        }
    }

    public function download(string $filename)
    {
        $path = $this->backupPath . '/' . $filename;

        if (!Storage::disk($this->backupDisk)->exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        ActivityLog::log('download', "Mengunduh file backup: {$filename}");

        return Storage::disk($this->backupDisk)->download($path, $filename);
    }

    public function destroy(string $filename): RedirectResponse
    {
        $path = $this->backupPath . '/' . $filename;

        if (Storage::disk($this->backupDisk)->exists($path)) {
            Storage::disk($this->backupDisk)->delete($path);
            ActivityLog::log('delete', "Menghapus file backup: {$filename}");
        }

        return back()->with('success', "File backup {$filename} berhasil dihapus.");
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate(['backup_file' => 'required|file|mimes:sql,sqlite,db']);

        try {
            $file   = $request->file('backup_file');
            $dbPath = database_path('database.sqlite');

            // For SQLite
            if (config('database.default') === 'sqlite') {
                copy($file->getRealPath(), $dbPath);
                ActivityLog::log('restore', 'Restore database dari file backup');
                return back()->with('success', 'Database berhasil direstore.');
            }

            return back()->with('error', 'Fitur restore untuk MySQL memerlukan konfigurasi tambahan di server.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat restore: ' . $e->getMessage());
        }
    }
}