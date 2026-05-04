<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Dokumen extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_AKTIF        = 'aktif';
    const STATUS_TINDAK_LANJUT = 'tindak_lanjut';
    const STATUS_SELESAI      = 'selesai';
    const STATUS_DISETUJUI    = 'disetujui';

    protected $fillable = [
        'no_urut',
        'no_surat',
        'perihal',
        'tanggal_surat',
        'tanggal_masuk',
        'asal_surat',
        'tujuan_surat',
        'kategori_id',
        'jenis',
        'status',
        'keterangan',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'uploaded_by',
        'approved_by',
        'approved_at',
        'tags',
    ];

    protected $casts = [
        'tanggal_surat'  => 'date',
        'tanggal_masuk'  => 'date',
        'approved_at'    => 'datetime',
        'tags'           => 'array',
    ];

    // Relationships
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('perihal', 'like', "%{$keyword}%")
              ->orWhere('no_surat', 'like', "%{$keyword}%")
              ->orWhere('asal_surat', 'like', "%{$keyword}%")
              ->orWhere('tujuan_surat', 'like', "%{$keyword}%")
              ->orWhere('keterangan', 'like', "%{$keyword}%");
        });
    }

    public function scopeByKategori($query, ?int $kategoriId)
    {
        if ($kategoriId) {
            return $query->where('kategori_id', $kategoriId);
        }
        return $query;
    }

    public function scopeByJenis($query, ?string $jenis)
    {
        if ($jenis) {
            return $query->where('jenis', $jenis);
        }
        return $query;
    }

    public function scopeByStatus($query, ?string $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('tanggal_surat', '>=', $from);
        }
        if ($to) {
            $query->whereDate('tanggal_surat', '<=', $to);
        }
        return $query;
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AKTIF         => 'Aktif',
            self::STATUS_TINDAK_LANJUT => 'Tindak Lanjut',
            self::STATUS_SELESAI       => 'Selesai',
            self::STATUS_DISETUJUI     => 'Disetujui',
            default                    => 'Aktif',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AKTIF         => 'badge-success',
            self::STATUS_TINDAK_LANJUT => 'badge-warning',
            self::STATUS_SELESAI       => 'badge-secondary',
            self::STATUS_DISETUJUI     => 'badge-primary',
            default                    => 'badge-secondary',
        };
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'masuk'  => 'Surat Masuk',
            'keluar' => 'Surat Keluar',
            'sk'     => 'SK Kepala Desa',
            default  => ucfirst($this->jenis),
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 1) . ' MB';
    }

    public function getFileIconAttribute(): string
    {
        return match (strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION))) {
            'pdf'        => '📄',
            'doc', 'docx'=> '📝',
            'xls', 'xlsx'=> '📊',
            'jpg', 'jpeg', 'png' => '🖼️',
            default      => '📎',
        };
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function isPreviewable(): bool
    {
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return in_array($ext, ['pdf', 'jpg', 'jpeg', 'png']);
    }
}