window.PendaftaranSearch = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $(document).on('keyup', PendaftaranConfig.elements.search, this.handleSearch.bind(this));
    },

    handleSearch: function(e) {
        const query = $(e.target).val();

        if (query.length >= 2) {
            $(PendaftaranConfig.elements.loading).show();

            PendaftaranHelpers.ajax(PendaftaranConfig.routes.CARI_PASIEN, {
                data: { q: query },
                success: function(data) {
                    setTimeout(function() {
                        $(PendaftaranConfig.elements.loading).hide();
                        $(PendaftaranConfig.elements.data).show().html(data);
                    }, 1000);
                },
                error: function() {
                    $(PendaftaranConfig.elements.loading).hide();
                    PendaftaranHelpers.showAlert('Gagal mencari data pasien', 'error');
                }
            });
        } else {
            $(PendaftaranConfig.elements.loading).hide();
            $(PendaftaranConfig.elements.data).hide();
        }
    }
};
