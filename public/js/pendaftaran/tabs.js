window.PendaftaranTabs = {
    init: function() {
        this.bindEvents();
        this.loadRawatJalan(); // Load initial data
    },

    bindEvents: function() {
        $(PendaftaranConfig.elements.tabRawatJalan).on("click", this.loadRawatJalan.bind(this));
        $(PendaftaranConfig.elements.tabRawatInap).on("click", this.loadRawatInap.bind(this));
        $(PendaftaranConfig.elements.tabRawatDarurat).on("click", this.loadRawatDarurat.bind(this));
    },

    loadRawatJalan: function() {
        PendaftaranHelpers.ajax(PendaftaranConfig.routes.SHOW_RAWAT_JALAN, {
            success: function(data) {
                $(PendaftaranConfig.elements.showRawatJalan).html(data);
            },
            showLoading: true,
            loadingTarget: PendaftaranConfig.elements.showRawatJalan
        });
    },

    loadRawatInap: function() {
        PendaftaranHelpers.ajax(PendaftaranConfig.routes.SHOW_RAWAT_INAP, {
            success: function(data) {
                $(PendaftaranConfig.elements.showRawatInap).html(data);
            },
            showLoading: true,
            loadingTarget: PendaftaranConfig.elements.showRawatInap
        });
    },

    loadRawatDarurat: function() {
        PendaftaranHelpers.ajax(PendaftaranConfig.routes.SHOW_RAWAT_DARURAT, {
            success: function(data) {
                $(PendaftaranConfig.elements.showRawatDarurat).html(data);
            },
            showLoading: true,
            loadingTarget: PendaftaranConfig.elements.showRawatDarurat
        });
    }
};
