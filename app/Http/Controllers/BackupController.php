<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    private string $backupDisk = 's3';
    private string $backupPath = 'backups';

    public function index(): View
    {
        $files = [];
        try {
            $rawFiles = Storage::disk($this->backupDisk)->files($this->backupPath);
            foreach ($rawFiles as $file) {
                $files[] = [
                    'name'     => basename($file),
                    'path'     => $file,
                    'size'     => Storage::disk($this->backupDisk)->size($file),
                    'modified' => Storage::disk($this->backupDisk)->lastModified($file),
                ];
            }
            usort($files, fn($a, $b) => $b['modified'] <=> $a['modified']);
        } catch (\Exception $e) {
            // S3 belum dikonfigurasi atau kosong — tampilkan halaman kosong
        }

        return view('backup.index', compact('files'));
    }

    public function create(Request $request): RedirectResponse
    {
        try {
            $filename   = 'backup_arsip_cumibakar_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $backupFile = $this->backupPath . '/' . $filename;

            $sql = $this->generateSqlDump();

            Storage::disk($this->backupDisk)->put($backupFile, $sql);

            ActivityLog::log('backup', "Membuat backup database: {$filename}");

            return back()->with('success', "Backup berhasil dibuat: {$filename}");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
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
        return back()->with('error', 'Fitur restore manual tidak tersedia di environment ini. Gunakan dashboard Supabase untuk restore data.');
    }

    // =============================================
    // Ekspor database menggunakan PDO (tanpa mysqldump)
    // Bekerja di Windows, Mac, Linux, dan Vercel serverless
    // =============================================
    private function generateSqlDump(): string
    {
        $connection = config('database.default');
        $tables     = DB::select('SHOW TABLES');
        $dbName     = config("database.connections.{$connection}.database");
        $tableKey   = "Tables_in_{$dbName}";

        $output  = "-- Backup Arsip Digital Desa Cumibakar\n";
        $output .= "-- Dibuat pada: " . now()->format('Y-m-d H:i:s') . "\n";
        $output .= "-- Database: {$dbName}\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableRow) {
            $tableName = $tableRow->$tableKey;

            // DROP + CREATE TABLE
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $output .= $createTable[0]->{'Create Table'} . ";\n\n";

            // INSERT rows
            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) continue;

            $output .= "INSERT INTO `{$tableName}` VALUES\n";
            $rowStrings = [];

            foreach ($rows as $row) {
                $values = array_map(function ($val) {
                    if ($val === null) return 'NULL';
                    return "'" . addslashes((string) $val) . "'";
                }, (array) $row);

                $rowStrings[] = '(' . implode(', ', $values) . ')';
            }

            $output .= implode(",\n", $rowStrings) . ";\n\n";
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $output;
    }
}