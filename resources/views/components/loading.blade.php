{{-- Loading Component yang dapat digunakan ulang --}}
<div class="loading-overlay {{ $class ?? '' }}" id="{{ $id ?? 'loading-overlay' }}" style="{{ $style ?? 'display: none;' }}">
    <div class="loading-content">
        <div class="spinner-border {{ $spinnerClass ?? 'text-primary' }}" role="status" aria-hidden="true">
            <span class="visually-hidden">Loading...</span>
        </div>
        @if(isset($message))
            <p class="mt-2 text-muted">{{ $message }}</p>
        @endif
    </div>
</div>

@push('style')
<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-content {
    text-align: center;
}
</style>
@endpush