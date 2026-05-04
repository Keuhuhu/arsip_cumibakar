@extends('app')

@section('title', 'Log Aktivitas Sistem')
@section('page-title', 'Log Aktivitas')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">🔍 Rekam Jejak Aktivitas Sistem</span>
    </div>

    {{-- Filter Form --}}
    <div class="card-body" style="border-bottom: 1px solid var(--border-color); background: #fafafa;">
        <form method="GET" action="{{ route('log.index') }}" class="filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
            
            <div class="form-group" style="margin-bottom:0; min-width:160px;">
                <label for="user_id" class="form-label" style="font-size:11px;">Pengguna</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Semua Pengguna</option>
                    @foreach($pengguna as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role_label }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0; min-width:140px;">
                <label for="action" class="form-label" style="font-size:11px;">Jenis Aktivitas</label>
                <select name="action" id="action" class="form-control">
                    <option value="">Semua Aktivitas</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0; min-width:130px;">
                <label for="dari" class="form-label" style="font-size:11px;">Dari Tanggal</label>
                <input type="date" name="dari" id="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            
            <div class="form-group" style="margin-bottom:0; min-width:130px;">
                <label for="sampai" class="form-label" style="font-size:11px;">Sampai Tanggal</label>
                <input type="date" name="sampai" id="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>

            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:38px;">Saring</button>
                @if(request()->anyFilled(['user_id', 'action', 'dari', 'sampai']))
                    <a href="{{ route('log.index') }}" class="btn btn-outline-danger" style="height:38px; line-height:22px;" title="Reset Filter">✕</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="160">Waktu Kejadian</th>
                        <th width="200">Pengguna</th>
                        <th width="100">Aksi</th>
                        <th>Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="font-size:12px; color:var(--text-soft); white-space:nowrap;">
                            {{ $log->created_at->format('d M Y, H:i:s') }}
                            <br>
                            <span style="font-size:10px;">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if($log->user)
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="user-avatar" style="width:28px; height:28px; font-size:11px;">{{ strtoupper(substr($log->user->name, 0, 2)) }}</span>
                                <div>
                                    <div style="font-weight:600; color:var(--text-dark);">{{ $log->user->name }}</div>
                                    <div style="font-size:11px; color:var(--text-soft);">{{ $log->user->role_label }}</div>
                                </div>
                            </div>
                            @else
                            <div style="color:var(--text-soft); font-style:italic;">System / Dihapus</div>
                            @endif
                        </td>
                        <td><span class="badge {{ $log->action_badge }}">{{ $log->action_label }}</span></td>
                        <td>
                            <div style="color:var(--text-dark);">{{ $log->description }}</div>
                            @if(!empty($log->properties))
                            <div style="margin-top:6px; font-size:11px; color:var(--text-soft); background:#f9fafb; padding:6px; border-radius:4px; border:1px solid #e5e7eb; word-break:break-all;">
                                {{ json_encode($log->properties) }}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-empty">
                            <span class="empty-icon">🔍</span>
                            Tidak ada log aktivitas yang tercatat
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($logs->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border-color);">
        {{ $logs->appends(request()->all())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
