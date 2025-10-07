{{-- Global Error Handler Component --}}
@push('scripts')
<script>
/**
 * Global Error Handler untuk Bambudua SIMRS
 */
window.BambuduaErrorHandler = {
    
    /**
     * Initialize error handlers
     */
    init: function() {
        // JavaScript Errors
        window.addEventListener('error', this.handleJSError);
        window.addEventListener('unhandledrejection', this.handlePromiseRejection);
        
        // AJAX Errors
        if (typeof $ !== 'undefined') {
            $(document).ajaxError(this.handleAjaxError);
        }
        
        console.log('✅ Error Handler initialized');
    },

    /**
     * Handle JavaScript errors
     */
    handleJSError: function(event) {
        console.error('JavaScript Error:', {
            message: event.message,
            filename: event.filename,
            line: event.lineno,
            column: event.colno,
            error: event.error
        });
        
        // Send to logging service if available
        BambuduaErrorHandler.logError('javascript', {
            message: event.message,
            filename: event.filename,
            line: event.lineno,
            column: event.colno,
            stack: event.error?.stack
        });
    },

    /**
     * Handle Promise rejections
     */
    handlePromiseRejection: function(event) {
        console.error('Unhandled Promise Rejection:', event.reason);
        
        BambuduaErrorHandler.logError('promise', {
            reason: event.reason,
            stack: event.reason?.stack
        });
    },

    /**
     * Handle AJAX errors
     */
    handleAjaxError: function(event, jqXHR, ajaxSettings, thrownError) {
        console.error('AJAX Error:', {
            url: ajaxSettings.url,
            method: ajaxSettings.type,
            status: jqXHR.status,
            statusText: jqXHR.statusText,
            response: jqXHR.responseText,
            error: thrownError
        });

        let errorMessage = 'Terjadi kesalahan dalam memuat data';
        
        // Handle specific error cases
        switch(jqXHR.status) {
            case 0:
                errorMessage = 'Tidak dapat terhubung ke server';
                break;
            case 401:
                errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.';
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
                break;
            case 403:
                errorMessage = 'Anda tidak memiliki akses untuk melakukan aksi ini';
                break;
            case 404:
                errorMessage = 'Data yang diminta tidak ditemukan';
                break;
            case 422:
                // Laravel validation errors
                try {
                    const response = JSON.parse(jqXHR.responseText);
                    if (response.errors) {
                        BambuduaErrorHandler.showValidationErrors(response.errors);
                        return;
                    }
                } catch(e) {
                    // Continue with generic error
                }
                errorMessage = 'Data yang Anda masukkan tidak valid';
                break;
            case 500:
                errorMessage = 'Terjadi kesalahan pada server. Tim teknis telah diberitahu.';
                break;
            case 502:
            case 503:
            case 504:
                errorMessage = 'Server sedang mengalami gangguan. Coba lagi beberapa saat.';
                break;
        }

        if (typeof BambuduaUtils !== 'undefined') {
            BambuduaUtils.showToast(errorMessage, 'error', 5000);
            BambuduaUtils.hideOverlayLoading();
        }
        
        BambuduaErrorHandler.logError('ajax', {
            url: ajaxSettings.url,
            method: ajaxSettings.type,
            status: jqXHR.status,
            response: jqXHR.responseText,
            error: thrownError
        });
    },

    /**
     * Show validation errors in form
     */
    showValidationErrors: function(errors) {
        // Clear previous errors
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');
        
        for (const [field, messages] of Object.entries(errors)) {
            const input = $(`[name="${field}"], [name="${field}[]"]`);
            if (input.length) {
                input.addClass('is-invalid');
                const errorDiv = $('<div class="invalid-feedback d-block"></div>');
                errorDiv.text(Array.isArray(messages) ? messages[0] : messages);
                input.closest('.form-group, .mb-3, .input-group').append(errorDiv);
            }
        }
        
        // Focus on first error field
        $('.is-invalid').first().focus();
    },

    /**
     * Log error to server (if endpoint available)
     */
    logError: function(type, details) {
        try {
            // Only log in production or if debug logging is enabled
            if (typeof APP_DEBUG !== 'undefined' && !APP_DEBUG) {
                return;
            }

            const logData = {
                type: type,
                details: details,
                url: window.location.href,
                userAgent: navigator.userAgent,
                timestamp: new Date().toISOString(),
                userId: window.currentUserId || null // Set this in main layout if needed
            };

            // Send to logging endpoint (create this route if needed)
            fetch('/api/log-client-error', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(logData)
            }).catch(() => {
                // Fail silently for logging errors
            });
        } catch (e) {
            // Fail silently for logging errors
        }
    },

    /**
     * Show user-friendly error page
     */
    showErrorPage: function(message = 'Terjadi kesalahan yang tidak terduga') {
        const errorHtml = `
            <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 60vh;">
                <div class="text-center">
                    <i class="ri-error-warning-line" style="font-size: 4rem; color: #dc3545;"></i>
                    <h3 class="mt-3">Oops! Terjadi Kesalahan</h3>
                    <p class="text-muted">${message}</p>
                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="ri-refresh-line"></i> Coba Lagi
                    </button>
                    <button class="btn btn-outline-secondary ms-2" onclick="history.back()">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </button>
                </div>
            </div>
        `;
        
        document.body.innerHTML = errorHtml;
    }
};

// Auto initialize
document.addEventListener('DOMContentLoaded', function() {
    BambuduaErrorHandler.init();
});
</script>
@endpush