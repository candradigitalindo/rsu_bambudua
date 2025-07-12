window.PendaftaranConfig = {
    routes: window.PENDAFTARAN_ROUTES || {},
    csrf: window.CSRF_TOKEN || '',

    // Elements
    elements: {
        loading: '#loading',
        data: '#data',
        search: '#search',

        // Forms
        formPasien: '#formpasien',
        formEditPasien: '#form-edit-pasien',

        // Tabs
        tabRawatJalan: '#tab-rawatJalan',
        tabRawatInap: '#tab-rawatInap',
        tabRawatDarurat: '#tab-igd',

        // Tables
        showRawatJalan: '#showRawatJalan',
        showRawatInap: '#showRawatInap',
        showRawatDarurat: '#showRawatDarurat',

        // Antrian
        antrian: '#antrian',
        jumlah: '#jumlah',
        btnNext: '#btn-next'
    },

    // Messages
    messages: {
        confirmDelete: "Apakah Anda Yakin?",
        confirmDeleteText: "Data yang dihapus tidak dapat dikembalikan!",
        loading: "Memuat data...",
        saving: "Menyimpan data...",
        success: "Berhasil",
        error: "Terjadi kesalahan"
    },

    // Validation rules
    validation: {
        required: ['name_pasien', 'jenis_kelamin', 'tgl_lahir', 'alamat', 'province', 'city', 'no_hp'],
        patterns: {
            phone: /^[0-9]{9,13}$/,
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            kodePos: /^[0-9]{5}$/
        }
    }
};

// Ensure config is available globally
console.log('✅ PendaftaranConfig loaded');
