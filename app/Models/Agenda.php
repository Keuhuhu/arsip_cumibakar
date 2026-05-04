<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_agenda',
        'jenis',
        'no_surat',
        'tanggal_surat',
        'tanggal_agenda',
        'perihal',
        'asal',
        'tujuan',
        'keterangan',
        'dokumen_id',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat'  => 'date',
        'tanggal_agenda' => 'date',
    ];

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Auto generate nomor agenda
    public static function generateNoAgenda(string $jenis, int $year): string
    {
        $prefix  = $jenis === 'masuk' ? 'SM' : 'SK';
        $lastNo  = static::where('jenis', $jenis)
                         ->whereYear('tanggal_agenda', $year)
                         ->max('no_agenda');

        if ($lastNo) {
            preg_match('/(\d+)$/', $lastNo, $matches);
            $nextNum = intval($matches[1] ?? 0) + 1;
        } else {
            $nextNum = 1;
        }

        return sprintf('%s/%04d/%s', $prefix, $nextNum, $year);
    }

    public function getJenisLabelAttribute(): string
    {
        return $this->jenis === 'masuk' ? 'Surat Masuk' : 'Surat Keluar';
    }
}