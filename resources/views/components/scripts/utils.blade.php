{{-- JavaScript Utils untuk optimasi loading dan UX --}}
@push('scripts')
<script>
/**
 * Bambudua Utils - JavaScript utilities untuk optimasi UX
 */
window.BambuduaUtils = {
    
    /**
     * Show loading pada button dengan animasi
     */
    showButtonLoading: function(buttonSelector, loadingText = 'Mohon Tunggu...') {
        const $btn = $(buttonSelector);
        const originalText = $btn.find('.btn-text').text() || $btn.text();
        
        $btn.prop('disabled', true)
            .addClass('disabled')
            .find('.spinner-border').removeClass('d-none');
            
        if ($btn.find('.btn-text').length) {
            $btn.find('.btn-text').text(loadingText);
        } else {
            $btn.data('original-text', originalText).text(loadingText);
        }
        
        return originalText;
    },

    /**
     * Hide loading pada button
     */
    hideButtonLoading: function(buttonSelector, originalText = null) {
        const $btn = $(buttonSelector);
        
        $btn.prop('disabled', false)
            .removeClass('disabled')
            .find('.spinner-border').addClass('d-none');
            
        if (originalText) {
            if ($btn.find('.btn-text').length) {
                $btn.find('.btn-text').text(originalText);
            } else {
                $btn.text(originalText);
            }
        } else {
            const stored = $btn.data('original-text');
            if (stored) {
                $btn.text(stored);
            }
        }
    },

    /**
     * Show overlay loading
     */
    showOverlayLoading: function(message = 'Memuat data...') {
        if (!$('#global-loading-overlay').length) {
            $('body').append(`
                <div id="global-loading-overlay" class="loading-overlay">
                    <div class="loading-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted" id="loading-message">${message}</p>
                    </div>
                </div>
            `);
        } else {
            $('#loading-message').text(message);
        }
        $('#global-loading-overlay').fadeIn(200);
    },

    /**
     * Hide overlay loading
     */
    hideOverlayLoading: function() {
        $('#global-loading-overlay').fadeOut(200);
    },

    /**
     * Show toast notification
     */
    showToast: function(message, type = 'success', duration = 3000) {
        const iconMap = {
            'success': 'ri-check-line',
            'error': 'ri-error-warning-line',
            'warning': 'ri-alert-line',
            'info': 'ri-information-line'
        };
        
        const toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="${iconMap[type]} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        if (!$('#toast-container').length) {
            $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        
        $('#toast-container').append(toast);
        
        const bsToast = new bootstrap.Toast(toast[0], {
            delay: duration
        });
        bsToast.show();
        
        // Remove after hiding
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    },

    /**
     * Konfirmasi SweetAlert dengan styling konsisten
     */
    confirmAction: function(title, text, confirmText = 'Ya, Lanjutkan', cancelText = 'Batal') {
        return swal({
            title: title,
            text: text,
            icon: "warning",
            buttons: {
                cancel: {
                    text: cancelText,
                    value: false,
                    visible: true,
                    className: "btn btn-secondary",
                    closeModal: true,
                },
                confirm: {
                    text: confirmText,
                    value: true,
                    visible: true,
                    className: "btn btn-danger",
                    closeModal: true
                }
            },
            dangerMode: true,
        });
    },

    /**
     * Format number dengan thousand separator
     */
    formatNumber: function(num, separator = '.') {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
    },

    /**
     * Debounce function untuk search
     */
    debounce: function(func, wait, immediate) {
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
    },

    /**
     * Auto resize textarea
     */
    autoResizeTextarea: function(selector) {
        $(selector).on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    },

    /**
     * Initialize table responsive scroll
     */
    initResponsiveTable: function() {
        $('.table-responsive').each(function() {
            const $table = $(this);
            if ($table.find('table').width() > $table.width()) {
                $table.addClass('table-scroll-indicator');
            }
        });
    }
};

// Auto initialize pada document ready
$(document).ready(function() {
    // Initialize responsive tables
    BambuduaUtils.initResponsiveTable();
    
    // Auto resize all textareas
    BambuduaUtils.autoResizeTextarea('textarea[data-auto-resize]');
    
    // Global AJAX error handler
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        console.error('AJAX Error:', thrownError);
        BambuduaUtils.hideOverlayLoading();
        BambuduaUtils.showToast('Terjadi kesalahan dalam memuat data', 'error');
    });
    
    console.log('✅ BambuduaUtils initialized');
});
</script>

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

.table-scroll-indicator::after {
    content: '← Geser untuk melihat lebih →';
    display: block;
    text-align: center;
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.5rem;
}

/* Toast positioning */
#toast-container {
    z-index: 9999;
}
</style>
@endpush