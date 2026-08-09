@props(['type' => 'success', 'message' => ''])

@if(session('success') || session('error') || $message)
    <div class="alert alert-{{ session('error') ? 'danger' : ($type === 'error' ? 'danger' : 'success') }} alert-dismissible fade show" role="alert">
        {{ $message ?: session('success') ?: session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
