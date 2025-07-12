window.PendaftaranHelpers = {
    // AJAX Helper
    ajax: function(url, options = {}) {
        const defaultOptions = {
            url: url,
            type: 'GET',
            data: { _token: PendaftaranConfig.csrf },
            dataType: 'json',
            beforeSend: function() {
                if (options.showLoading) {
                    PendaftaranHelpers.showLoading(options.loadingTarget);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                PendaftaranHelpers.showAlert('Terjadi kesalahan saat memuat data', 'error');
            }
        };

        return $.ajax($.extend(defaultOptions, options));
    },

    // Alert Helper
    showAlert: function(message, type = 'success') {
        const icon = type === 'error' ? 'error' : 'success';
        swal(message, { icon: icon });
    },

    // Loading Helper
    showLoading: function(selector) {
        $(selector).html(`
            <div class="text-center p-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">${PendaftaranConfig.messages.loading}</p>
            </div>
        `);
    },

    hideLoading: function(selector) {
        $(selector).empty();
    },

    // Form Helper
    resetForm: function(formId) {
        $(formId)[0].reset();
        $(formId).find('.is-invalid').removeClass('is-invalid');
        $(formId).find('.invalid-feedback').remove();
    },

    // Error Helper
    showFormErrors: function(errors, container) {
        $(container).find("ul").html('');
        $(container).css('display', 'block');
        $.each(errors, function(key, value) {
            $(container).find("ul").append('<li>' + value + '</li>');
        });
    },

    hideFormErrors: function(container) {
        $(container).css('display', 'none');
    },

    // URL Helper
    buildUrl: function(template, params) {
        let url = template;
        for (let key in params) {
            url = url.replace(':' + key, params[key]);
        }
        return url;
    },

    // Confirm Dialog
    confirmDelete: function(callback) {
        swal({
            title: PendaftaranConfig.messages.confirmDelete,
            text: PendaftaranConfig.messages.confirmDeleteText,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete && callback) {
                callback();
            }
        });
    },

    // Enhanced form validation
    validateField: function(field, rules = {}) {
        const $field = $(field);
        const value = $field.val();
        let isValid = true;
        let message = '';

        // Required validation
        if (rules.required && !value) {
            isValid = false;
            message = 'Field ini wajib diisi';
        }

        // Pattern validation
        if (value && rules.pattern && !rules.pattern.test(value)) {
            isValid = false;
            message = rules.message || 'Format tidak valid';
        }

        // Min/Max length validation
        if (value) {
            if (rules.minLength && value.length < rules.minLength) {
                isValid = false;
                message = `Minimal ${rules.minLength} karakter`;
            }
            if (rules.maxLength && value.length > rules.maxLength) {
                isValid = false;
                message = `Maksimal ${rules.maxLength} karakter`;
            }
        }

        // Update field validation state
        $field.removeClass('is-valid is-invalid');
        $field.next('.invalid-feedback').remove();

        if (isValid) {
            $field.addClass('is-valid');
        } else {
            $field.addClass('is-invalid');
            $field.after(`<div class="invalid-feedback">${message}</div>`);
        }

        return isValid;
    },

    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },

    // Format date
    formatDate: function(date, format = 'DD/MM/YYYY') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();

        switch (format) {
            case 'DD/MM/YYYY':
                return `${day}/${month}/${year}`;
            case 'YYYY-MM-DD':
                return `${year}-${month}-${day}`;
            default:
                return d.toLocaleDateString('id-ID');
        }
    },

    // Debounce function for search
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};
