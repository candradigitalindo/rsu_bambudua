{{-- Alert Component yang dapat digunakan ulang --}}
@props([
    'type' => 'info', // success, danger, warning, info, primary, secondary
    'dismissible' => true,
    'icon' => null,
    'title' => null,
    'fade' => true
])

@php
$iconMap = [
    'success' => 'ri-check-line',
    'danger' => 'ri-error-warning-line',
    'warning' => 'ri-alert-line',
    'info' => 'ri-information-line',
    'primary' => 'ri-information-line',
    'secondary' => 'ri-information-line'
];

$defaultIcon = $icon ?? ($iconMap[$type] ?? 'ri-information-line');
@endphp

<div class="alert alert-{{ $type }} {{ $dismissible ? 'alert-dismissible' : '' }} {{ $fade ? 'fade show' : '' }}" role="alert">
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
    
    <div class="d-flex align-items-start">
        @if($defaultIcon)
            <i class="{{ $defaultIcon }} me-2 flex-shrink-0" style="margin-top: 2px;"></i>
        @endif
        
        <div class="flex-grow-1">
            @if($title)
                <h6 class="alert-heading mb-1">{{ $title }}</h6>
            @endif
            
            {{ $slot }}
        </div>
    </div>
</div>

@if($dismissible)
    @push('scripts')
    <script>
        // Auto hide dismissible alerts after 5 seconds unless user interacts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                let autoHide = setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
                
                // Cancel auto hide if user hovers
                alert.addEventListener('mouseenter', function() {
                    clearTimeout(autoHide);
                });
            });
        });
    </script>
    @endpush
@endif