@extends('layouts.portal')

@section('title', 'Notifikasi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Notifikasi</h1>
    <a href="{{ route('portal.index') }}" class="btn btn-sm btn-secondary shadow-sm">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body p-0">
        @forelse($notifications as $notification)
            <div class="border-bottom px-4 py-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 {{ $notification->read_at ? 'text-muted' : 'font-weight-bold text-gray-800' }}">
                            {{ $notification->data['message'] ?? 'Notifikasi' }}
                        </p>
                        @if(isset($notification->data['student_name']))
                            <small class="text-muted">Siswa: {{ $notification->data['student_name'] }}</small><br>
                        @endif
                        @if(isset($notification->data['judul']))
                            <small class="text-muted">Kasus: {{ $notification->data['judul'] }}</small><br>
                        @endif
                        <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                        @if(isset($notification->data['student_id']) && ! $notification->read_at)
                            <div class="mt-2">
                                <a href="{{ route('portal.child', $notification->data['student_id']) }}" class="btn btn-xs btn-sm btn-primary">
                                    Lihat Detail
                                </a>
                            </div>
                        @endif
                    </div>
                    @if(! $notification->read_at)
                        <form action="{{ route('portal.notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Tandai dibaca</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-2x mb-3"></i>
                <p class="mb-0">Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
        <div class="card-footer">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
