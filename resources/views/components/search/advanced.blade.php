{{-- Advanced Search Component --}}
@props([
    'action' => null,
    'method' => 'GET',
    'placeholder' => 'Cari...',
    'name' => 'q',
    'value' => null,
    'debounce' => 500,
    'minLength' => 2,
    'showButton' => true,
    'showClearButton' => true,
    'filters' => [],
    'ajaxUrl' => null,
    'resultContainer' => null,
    'onResults' => null,
    'noResultsText' => 'Tidak ada hasil ditemukan',
    'loadingText' => 'Mencari...',
    'containerClass' => 'mb-3',
    'inputClass' => '',
    'buttonClass' => 'btn-outline-primary'
])

@php
$searchId = 'search-' . uniqid();
$hasFilters = !empty($filters);
@endphp

<div class="{{ $containerClass }}">
    <form method="{{ $method }}" @if($action) action="{{ $action }}" @endif class="search-form" id="{{ $searchId }}-form">
        @if($method !== 'GET')
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif
        @endif
        
        <div class="row g-2 align-items-end">
            {{-- Search Input --}}
            <div class="col-md-6">
                <div class="position-relative">
                    <input 
                        type="text" 
                        name="{{ $name }}"
                        id="{{ $searchId }}"
                        class="form-control {{ $inputClass }}"
                        placeholder="{{ $placeholder }}"
                        value="{{ old($name, $value ?? request($name)) }}"
                        data-min-length="{{ $minLength }}"
                        data-debounce="{{ $debounce }}"
                        @if($ajaxUrl) data-ajax-url="{{ $ajaxUrl }}" @endif
                        @if($resultContainer) data-result-container="{{ $resultContainer }}" @endif
                        autocomplete="off"
                    >
                    
                    {{-- Loading Indicator --}}
                    <div class="position-absolute top-50 end-0 translate-middle-y me-2 d-none" id="{{ $searchId }}-loading">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">{{ $loadingText }}</span>
                        </div>
                    </div>
                    
                    {{-- Clear Button --}}
                    @if($showClearButton)
                    <button type="button" 
                            class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-1 p-1 d-none" 
                            id="{{ $searchId }}-clear"
                            title="Hapus pencarian">
                        <i class="ri-close-line"></i>
                    </button>
                    @endif
                </div>
                
                {{-- Search Suggestions --}}
                @if($ajaxUrl)
                <div class="position-relative">
                    <div class="list-group position-absolute w-100 shadow-sm d-none" 
                         id="{{ $searchId }}-suggestions" 
                         style="z-index: 1050; max-height: 300px; overflow-y: auto;">
                    </div>
                </div>
                @endif
            </div>

            {{-- Filters --}}
            @if($hasFilters)
                @foreach($filters as $filter)
                <div class="col-md-{{ $filter['col'] ?? '2' }}">
                    @if(isset($filter['label']))
                        <label class="form-label mb-1">{{ $filter['label'] }}</label>
                    @endif
                    
                    @if($filter['type'] === 'select')
                        <select name="{{ $filter['name'] }}" class="form-select form-select-sm">
                            <option value="">{{ $filter['placeholder'] ?? 'Semua' }}</option>
                            @foreach($filter['options'] as $value => $label)
                                <option value="{{ $value }}" 
                                        @if(request($filter['name']) == $value) selected @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filter['type'] === 'date')
                        <input type="date" 
                               name="{{ $filter['name'] }}" 
                               class="form-control form-control-sm"
                               value="{{ request($filter['name']) }}">
                    @elseif($filter['type'] === 'daterange')
                        <input type="date" 
                               name="{{ $filter['name'] }}_start" 
                               class="form-control form-control-sm mb-1"
                               placeholder="Dari tanggal"
                               value="{{ request($filter['name'] . '_start') }}">
                        <input type="date" 
                               name="{{ $filter['name'] }}_end" 
                               class="form-control form-control-sm"
                               placeholder="Sampai tanggal"
                               value="{{ request($filter['name'] . '_end') }}">
                    @else
                        <input type="{{ $filter['type'] ?? 'text' }}" 
                               name="{{ $filter['name'] }}" 
                               class="form-control form-control-sm"
                               placeholder="{{ $filter['placeholder'] ?? '' }}"
                               value="{{ request($filter['name']) }}">
                    @endif
                </div>
                @endforeach
            @endif

            {{-- Action Buttons --}}
            <div class="col-md-{{ $hasFilters ? '2' : '6' }}">
                @if($showButton)
                    <button type="submit" class="btn {{ $buttonClass }} btn-sm me-1">
                        <i class="ri-search-line"></i> Cari
                    </button>
                @endif
                
                @if($hasFilters || request()->hasAny(array_column($filters ?? [], 'name')))
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="{{ $searchId }}-reset">
                        <i class="ri-refresh-line"></i> Reset
                    </button>
                @endif
            </div>
        </div>
    </form>

    {{-- Results Container (untuk AJAX) --}}
    @if($ajaxUrl && $resultContainer)
    <div id="{{ $resultContainer }}" class="mt-3">
        <div id="{{ $searchId }}-no-results" class="text-center text-muted py-4 d-none">
            <i class="ri-search-line fs-3"></i>
            <p class="mb-0">{{ $noResultsText }}</p>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const searchForm = $('#{{ $searchId }}-form');
    const searchInput = $('#{{ $searchId }}');
    const loadingIndicator = $('#{{ $searchId }}-loading');
    const clearButton = $('#{{ $searchId }}-clear');
    const suggestionsContainer = $('#{{ $searchId }}-suggestions');
    const resetButton = $('#{{ $searchId }}-reset');
    
    let searchTimeout;
    let currentRequest;
    
    // Debounced search function with fallback
    const debounce = function(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };
    
    const debouncedSearch = (typeof BambuduaUtils !== 'undefined' && BambuduaUtils.debounce) 
        ? BambuduaUtils.debounce(function() {
            const query = searchInput.val().trim();
            const minLength = parseInt(searchInput.data('min-length'));
            
            if (query.length >= minLength) {
                performSearch(query);
            } else if (query.length === 0) {
                clearResults();
            }
        }, {{ $debounce }})
        : debounce(function() {
            const query = searchInput.val().trim();
            const minLength = parseInt(searchInput.data('min-length'));
            
            if (query.length >= minLength) {
                performSearch(query);
            } else if (query.length === 0) {
                clearResults();
            }
        }, {{ $debounce }});
    
    // Search input event
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        // Show/hide clear button
        if (query.length > 0) {
            clearButton.removeClass('d-none');
        } else {
            clearButton.addClass('d-none');
            clearResults();
        }
        
        // Perform debounced search for AJAX
        @if($ajaxUrl)
        debouncedSearch();
        @endif
    });
    
    // Clear button
    clearButton.on('click', function() {
        searchInput.val('').focus();
        clearButton.addClass('d-none');
        clearResults();
    });
    
    // Reset button
    resetButton.on('click', function() {
        searchForm[0].reset();
        clearButton.addClass('d-none');
        clearResults();
        
        @if(!$ajaxUrl)
        // Reload page without query parameters
        window.location.href = window.location.pathname;
        @endif
    });
    
    // Perform AJAX search
    @if($ajaxUrl)
    function performSearch(query) {
        // Cancel previous request
        if (currentRequest) {
            currentRequest.abort();
        }
        
        // Show loading
        loadingIndicator.removeClass('d-none');
        clearButton.addClass('d-none');
        
        // Get form data
        const formData = searchForm.serialize();
        
        currentRequest = $.ajax({
            url: '{{ $ajaxUrl }}',
            method: 'GET',
            data: formData,
            dataType: 'json'
        })
        .done(function(response) {
            loadingIndicator.addClass('d-none');
            clearButton.removeClass('d-none');
            
            if (typeof {{ $onResults ?? 'null' }} === 'function') {
                {{ $onResults }}(response);
            } else {
                // Check if response is HTML string (from controller)
                if (typeof response === 'string') {
                    $('#{{ $resultContainer }}').html(response);
                    $('#{{ $searchId }}-no-results').addClass('d-none');
                } else {
                    displayResults(response);
                }
            }
        })
        .fail(function(xhr) {
            loadingIndicator.addClass('d-none');
            clearButton.removeClass('d-none');
            
            if (xhr.statusText !== 'abort') {
                console.error('Search failed:', xhr);
                if (typeof BambuduaUtils !== 'undefined' && BambuduaUtils.showToast) {
                    BambuduaUtils.showToast('Gagal melakukan pencarian', 'error');
                } else {
                    // Fallback toast notification
                    const toast = $('<div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">')
                        .html('Gagal melakukan pencarian <button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
                    $('body').append(toast);
                    setTimeout(() => toast.alert('close'), 5000);
                }
            }
        });
    }
    
    function displayResults(response) {
        const container = $('#{{ $resultContainer }}');
        const noResults = $('#{{ $searchId }}-no-results');
        
        if (response.data && response.data.length > 0) {
            let html = '';
            response.data.forEach(function(item) {
                html += `
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <h6 class="card-title mb-1">${item.title || item.name}</h6>
                            <p class="card-text small text-muted mb-0">${item.description || ''}</p>
                        </div>
                    </div>
                `;
            });
            container.html(html);
            noResults.addClass('d-none');
        } else {
            container.empty();
            noResults.removeClass('d-none');
        }
    }
    @endif
    
    function clearResults() {
        @if($ajaxUrl)
        suggestionsContainer.addClass('d-none').empty();
        @if($resultContainer)
        $('#{{ $resultContainer }}').empty();
        $('#{{ $searchId }}-no-results').addClass('d-none');
        @endif
        @endif
    }
    
    // Click outside to hide suggestions
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#{{ $searchId }}-form').length) {
            suggestionsContainer.addClass('d-none');
        }
    });
});
</script>
@endpush