{{-- Export Buttons Component --}}
@props([
    'pdfUrl' => null,
    'excelUrl' => null,
    'csvUrl' => null,
    'printUrl' => null,
    'title' => 'Data',
    'buttonClass' => 'btn-sm',
    'dropdownClass' => 'btn-outline-secondary',
    'includeFilters' => true,
    'customExports' => [],
    'containerClass' => 'btn-group'
])

@php
$hasExports = $pdfUrl || $excelUrl || $csvUrl || $printUrl || !empty($customExports);
$exportId = 'export-' . uniqid();
@endphp

@if($hasExports)
<div class="{{ $containerClass }}" role="group">
    @if(count(array_filter([$pdfUrl, $excelUrl, $csvUrl, $printUrl])) + count($customExports) > 1)
        {{-- Multiple exports - use dropdown --}}
        <button type="button" class="btn {{ $dropdownClass }} {{ $buttonClass }} dropdown-toggle" 
                data-bs-toggle="dropdown" aria-expanded="false" id="{{ $exportId }}">
            <i class="ri-download-2-line"></i> Export {{ $title }}
        </button>
        <ul class="dropdown-menu">
            @if($pdfUrl)
            <li>
                <a class="dropdown-item export-link" 
                   href="{{ $includeFilters ? $pdfUrl . '?' . request()->getQueryString() : $pdfUrl }}"
                   data-type="pdf">
                    <i class="ri-file-pdf-line text-danger me-2"></i> Export PDF
                </a>
            </li>
            @endif
            
            @if($excelUrl)
            <li>
                <a class="dropdown-item export-link" 
                   href="{{ $includeFilters ? $excelUrl . '?' . request()->getQueryString() : $excelUrl }}"
                   data-type="excel">
                    <i class="ri-file-excel-2-line text-success me-2"></i> Export Excel
                </a>
            </li>
            @endif
            
            @if($csvUrl)
            <li>
                <a class="dropdown-item export-link" 
                   href="{{ $includeFilters ? $csvUrl . '?' . request()->getQueryString() : $csvUrl }}"
                   data-type="csv">
                    <i class="ri-file-text-line text-info me-2"></i> Export CSV
                </a>
            </li>
            @endif
            
            @if($customExports)
                @foreach($customExports as $export)
                <li>
                    <a class="dropdown-item export-link" 
                       href="{{ $export['url'] }}" 
                       data-type="{{ $export['type'] ?? 'custom' }}"
                       @if(isset($export['target'])) target="{{ $export['target'] }}" @endif>
                        @if(isset($export['icon']))
                            <i class="{{ $export['icon'] }} me-2"></i>
                        @endif
                        {{ $export['label'] }}
                    </a>
                </li>
                @endforeach
            @endif
            
            @if($printUrl || ($pdfUrl && !$printUrl))
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item export-link" 
                   href="{{ $printUrl ?: $pdfUrl }}" 
                   target="_blank"
                   data-type="print">
                    <i class="ri-printer-line text-secondary me-2"></i> Print
                </a>
            </li>
            @endif
        </ul>
    @else
        {{-- Single export - direct button --}}
        @if($pdfUrl)
            <a href="{{ $includeFilters ? $pdfUrl . '?' . request()->getQueryString() : $pdfUrl }}" 
               class="btn btn-danger {{ $buttonClass }} export-link" data-type="pdf">
                <i class="ri-file-pdf-line"></i> PDF
            </a>
        @endif
        
        @if($excelUrl)
            <a href="{{ $includeFilters ? $excelUrl . '?' . request()->getQueryString() : $excelUrl }}" 
               class="btn btn-success {{ $buttonClass }} export-link" data-type="excel">
                <i class="ri-file-excel-2-line"></i> Excel
            </a>
        @endif
        
        @if($csvUrl)
            <a href="{{ $includeFilters ? $csvUrl . '?' . request()->getQueryString() : $csvUrl }}" 
               class="btn btn-info {{ $buttonClass }} export-link" data-type="csv">
                <i class="ri-file-text-line"></i> CSV
            </a>
        @endif
        
        @if($printUrl)
            <a href="{{ $printUrl }}" target="_blank"
               class="btn btn-secondary {{ $buttonClass }} export-link" data-type="print">
                <i class="ri-printer-line"></i> Print
            </a>
        @endif
        
        @foreach($customExports as $export)
            <a href="{{ $export['url'] }}" 
               class="btn {{ $export['class'] ?? 'btn-outline-primary' }} {{ $buttonClass }} export-link" 
               data-type="{{ $export['type'] ?? 'custom' }}"
               @if(isset($export['target'])) target="{{ $export['target'] }}" @endif>
                @if(isset($export['icon']))
                    <i class="{{ $export['icon'] }}"></i>
                @endif
                {{ $export['label'] }}
            </a>
        @endforeach
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle export clicks
    $('.export-link').on('click', function(e) {
        const $link = $(this);
        const type = $link.data('type');
        const url = $link.attr('href');
        
        // Show loading indicator
        const originalText = $link.html();
        $link.html('<i class="ri-loader-4-line spinner"></i> Mengunduh...')
             .addClass('disabled')
             .css('pointer-events', 'none');
        
        // For PDF and print, open in new tab
        if (type === 'pdf' || type === 'print') {
            window.open(url, '_blank');
            
            // Reset button after delay
            setTimeout(function() {
                $link.html(originalText)
                     .removeClass('disabled')
                     .css('pointer-events', 'auto');
            }, 1500);
            
            return false;
        }
        
        // For Excel/CSV, create download link
        if (type === 'excel' || type === 'csv') {
            // Create invisible download link
            const downloadLink = document.createElement('a');
            downloadLink.href = url;
            downloadLink.download = '';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            
            // Reset button
            setTimeout(function() {
                $link.html(originalText)
                     .removeClass('disabled')
                     .css('pointer-events', 'auto');
                     
                BambuduaUtils.showToast('File berhasil diunduh', 'success');
            }, 1000);
            
            e.preventDefault();
            return false;
        }
        
        // For other types, proceed normally but track
        setTimeout(function() {
            $link.html(originalText)
                 .removeClass('disabled')
                 .css('pointer-events', 'auto');
        }, 2000);
    });
    
    // Handle dropdown state
    $('#{{ $exportId }}').on('show.bs.dropdown', function() {
        $(this).find('i').removeClass('ri-download-2-line').addClass('ri-loader-4-line spinner');
    }).on('hide.bs.dropdown', function() {
        $(this).find('i').removeClass('ri-loader-4-line spinner').addClass('ri-download-2-line');
    });
});
</script>

<style>
.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.export-link.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.dropdown-item:hover .ri-file-pdf-line,
.dropdown-item:hover .ri-file-excel-2-line,
.dropdown-item:hover .ri-file-text-line,
.dropdown-item:hover .ri-printer-line {
    transform: scale(1.1);
}
</style>
@endpush
@endif