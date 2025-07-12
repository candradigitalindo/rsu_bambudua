$(document).ready(function() {
    console.log('🚀 Initializing Pendaftaran Application...');

    // Initialize all modules with error handling
    try {
        if (typeof PendaftaranHelpers !== 'undefined') {
            console.log('✅ PendaftaranHelpers loaded');
        } else {
            throw new Error('PendaftaranHelpers not loaded');
        }

        if (typeof PendaftaranSearch !== 'undefined') {
            PendaftaranSearch.init();
            console.log('✅ PendaftaranSearch initialized');
        } else {
            console.warn('⚠️ PendaftaranSearch not available');
        }

        if (typeof PendaftaranForms !== 'undefined') {
            PendaftaranForms.init();
            console.log('✅ PendaftaranForms initialized');
        } else {
            console.warn('⚠️ PendaftaranForms not available');
        }

        if (typeof PendaftaranTabs !== 'undefined') {
            PendaftaranTabs.init();
            console.log('✅ PendaftaranTabs initialized');
        } else {
            console.warn('⚠️ PendaftaranTabs not available');
        }

        if (typeof PendaftaranEncounters !== 'undefined') {
            PendaftaranEncounters.init();
            console.log('✅ PendaftaranEncounters initialized');
        } else {
            console.warn('⚠️ PendaftaranEncounters not available');
        }

        if (typeof PendaftaranAntrian !== 'undefined') {
            PendaftaranAntrian.init();
            console.log('✅ PendaftaranAntrian initialized');
        } else {
            console.warn('⚠️ PendaftaranAntrian not available');
        }

        // Hide loading initially if element exists
        if ($(PendaftaranConfig.elements.loading).length) {
            $(PendaftaranConfig.elements.loading).hide();
        }

        console.log('🎉 Pendaftaran Application Ready');

    } catch (error) {
        console.error('❌ Error initializing Pendaftaran Application:', error);

        // Show user-friendly error message
        if (typeof swal !== 'undefined') {
            swal('Error', 'Terjadi kesalahan saat memuat aplikasi. Silakan refresh halaman.', 'error');
        } else {
            alert('Terjadi kesalahan saat memuat aplikasi. Silakan refresh halaman.');
        }
    }
});

// Global error handler for AJAX requests
$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
    console.error('AJAX Error:', {
        url: ajaxSettings.url,
        error: thrownError,
        status: jqXHR.status,
        response: jqXHR.responseText
    });

    if (typeof swal !== 'undefined') {
        swal('Error', 'Terjadi kesalahan koneksi. Silakan coba lagi.', 'error');
    }
});
