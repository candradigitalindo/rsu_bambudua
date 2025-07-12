window.PendaftaranEncounters = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        const self = this; // Store reference to 'this'

        // Rawat Jalan Events
        $(document).on('click', '.rawatJalan', function(e) {
            self.handleRawatJalan(e);
        });
        $(document).on('click', '.editrawatJalan', function(e) {
            self.handleEditRawatJalan(e);
        });
        $(document).on('click', '.destoryRawatJalan', function(e) {
            self.handleDeleteRawatJalan(e);
        });
        $(document).on('click', '#btn-submit-rawatJalan', function(e) {
            self.handleSubmitRawatJalan(e);
        });

        // Rawat Inap Events
        $(document).on('click', '.rawatInap', function(e) {
            self.handleRawatInap(e);
        });
        $(document).on('click', '.editrawatInap', function(e) {
            self.handleEditRawatInap(e);
        });
        $(document).on('click', '.destroyRawatInap', function(e) {
            self.handleDeleteRawatInap(e);
        });
        $(document).on('click', '#btn-submit-rawatInap', function(e) {
            self.handleSubmitRawatInap(e);
        });

        // Rawat Darurat Events
        $(document).on('click', '.igd', function(e) {
            self.handleRawatDarurat(e);
        });
        $(document).on('click', '.editrawatDarurat', function(e) {
            self.handleEditRawatDarurat(e);
        });
        $(document).on('click', '.destroyRawatDarurat', function(e) {
            self.handleDeleteRawatDarurat(e);
        });
        $(document).on('click', '#btn-submit-rawatDarurat', function(e) {
            self.handleSubmitRawatDarurat(e);
        });
    },

    // Rawat Jalan Methods
    handleRawatJalan: function(e) {
        const id = $(e.target).attr('id');
        this.loadPatientData(id, 'rawatJalan');
        this.resetForm('rawatJalan');
        $("#btn-submit-rawatJalan").text("Simpan");
    },

    handleEditRawatJalan: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.EDIT_ENCOUNTER_RAJAL, {id: id});
        const self = this;

        PendaftaranHelpers.ajax(url, {
            success: function(res) {
                $("#jenis_jaminan").val(res.data.jenis_jaminan);
                $("#dokter").val(res.data.dokter);
                $("#tujuan_kunjungan").val(res.data.tujuan_kunjungan);
                $("#id-rawatJalan").val(id);
                $("#btn-submit-rawatJalan").text("Update Rawat Jalan");

                // Load patient info
                self.fillPatientInfo(res.data, 'rawatJalan');
            }
        });
    },

    handleDeleteRawatJalan: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.DELETE_RAWAT_JALAN, {id: id});

        PendaftaranHelpers.confirmDelete(function() {
            PendaftaranHelpers.ajax(url, {
                type: 'DELETE',
                success: function(res) {
                    PendaftaranHelpers.showAlert(res.text, res.status ? 'success' : 'error');
                    if (res.status) {
                        PendaftaranTabs.loadRawatJalan();
                    }
                }
            });
        });
    },

    handleSubmitRawatJalan: function() {
        const isUpdate = $("#btn-submit-rawatJalan").text() === 'Update Rawat Jalan';

        if (isUpdate) {
            this.updateRawatJalan();
        } else {
            this.submitRawatJalan();
        }
    },

    submitRawatJalan: function() {
        const id = $("#id-rawatJalan").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.POST_RAWAT_JALAN, {id: id});
        const self = this;

        const formData = {
            _token: PendaftaranConfig.csrf,
            jenis_jaminan: $("#jenis_jaminan").val(),
            dokter: $("#dokter").val(),
            tujuan_kunjungan: $("#tujuan_kunjungan").val()
        };

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                self.handleEncounterResponse(res, 'rawatJalan');
            }
        });
    },

    updateRawatJalan: function() {
        const id = $("#id-rawatJalan").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.UPDATE_RAWAT_JALAN, {id: id});
        const self = this;

        const formData = {
            _token: PendaftaranConfig.csrf,
            jenis_jaminan: $("#jenis_jaminan").val(),
            dokter: $("#dokter").val(),
            tujuan_kunjungan: $("#tujuan_kunjungan").val()
        };

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                self.handleEncounterResponse(res, 'rawatJalan');
            }
        });
    },

    // Rawat Inap Methods
    handleRawatInap: function(e) {
        const id = $(e.target).attr('id');
        this.loadPatientData(id, 'rawatInap');
        this.resetForm('rawatInap');
        $("#btn-submit-rawatInap").text("Simpan");
    },

    handleEditRawatInap: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.EDIT_ENCOUNTER_RINAP, {id: id});
        const self = this;

        PendaftaranHelpers.ajax(url, {
            success: function(res) {
                $("#jenis_jaminan_rawatInap").val(res.data.jenis_jaminan);
                $("#dokter_rawatInap").val(res.data.dokter);
                $("#ruangan_rawatInap").val(res.data.ruangan);
                $("#tujuan_kunjungan_rawatInap").val(res.data.tujuan_kunjungan);
                $("#id-rawatInap").val(id);
                $("#btn-submit-rawatInap").text("Update Rawat Inap");

                self.fillPatientInfo(res.data, 'rawatInap');
            }
        });
    },

    handleDeleteRawatInap: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.DELETE_RAWAT_INAP, {id: id});

        PendaftaranHelpers.confirmDelete(function() {
            PendaftaranHelpers.ajax(url, {
                type: 'DELETE',
                success: function(res) {
                    PendaftaranHelpers.showAlert(res.text, res.status ? 'success' : 'error');
                    if (res.status) {
                        PendaftaranTabs.loadRawatInap();
                    }
                }
            });
        });
    },

    handleSubmitRawatInap: function() {
        const isUpdate = $("#btn-submit-rawatInap").text() === 'Update Rawat Inap';

        if (isUpdate) {
            this.updateRawatInap();
        } else {
            this.submitRawatInap();
        }
    },

    submitRawatInap: function() {
        const id = $("#id-rawatInap").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.POST_RAWAT_INAP, {id: id});
        const self = this;

        const formData = {
            _token: PendaftaranConfig.csrf,
            jenis_jaminan_rawatInap: $("#jenis_jaminan_rawatInap").val(),
            dokter_rawatInap: $("#dokter_rawatInap").val(),
            ruangan_rawatInap: $("#ruangan_rawatInap").val(),
            tujuan_kunjungan_rawatInap: $("#tujuan_kunjungan_rawatInap").val()
        };

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                self.handleEncounterResponse(res, 'rawatInap');
            }
        });
    },

    updateRawatInap: function() {
        const id = $("#id-rawatInap").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.UPDATE_RAWAT_INAP, {id: id});
        const self = this;

        const formData = {
            _token: PendaftaranConfig.csrf,
            jenis_jaminan_rawatInap: $("#jenis_jaminan_rawatInap").val(),
            dokter_rawatInap: $("#dokter_rawatInap").val(),
            ruangan_rawatInap: $("#ruangan_rawatInap").val(),
            tujuan_kunjungan_rawatInap: $("#tujuan_kunjungan_rawatInap").val()
        };

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                self.handleEncounterResponse(res, 'rawatInap');
            }
        });
    },

    // Rawat Darurat Methods
    handleRawatDarurat: function(e) {
        const id = $(e.target).attr('id');
        this.loadPatientData(id, 'rawatDarurat');
        this.resetForm('rawatDarurat');
        $("#btn-submit-rawatDarurat").text("Simpan");
    },

    handleEditRawatDarurat: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.EDIT_ENCOUNTER_RDARURAT, {id: id});
        const self = this;

        PendaftaranHelpers.ajax(url, {
            success: function(res) {
                $("#jenis_jaminan_rawatDarurat").val(res.data.jenis_jaminan);
                $("#dokter_rawatDarurat").val(res.data.dokter);
                $("#tingkat_kegawatan").val(res.data.tingkat_kegawatan);
                $("#keluhan_utama").val(res.data.keluhan_utama);
                $("#id-rawatDarurat").val(id);
                $("#btn-submit-rawatDarurat").text("Update Rawat Darurat");

                self.fillPatientInfo(res.data, 'rawatDarurat');
            }
        });
    },

    handleDeleteRawatDarurat: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.DELETE_RAWAT_DARURAT, {id: id});

        PendaftaranHelpers.confirmDelete(function() {
            PendaftaranHelpers.ajax(url, {
                type: 'DELETE',
                success: function(res) {
                    PendaftaranHelpers.showAlert(res.text, res.status ? 'success' : 'error');
                    if (res.status) {
                        PendaftaranTabs.loadRawatDarurat();
                    }
                }
            });
        });
    },

    handleSubmitRawatDarurat: function() {
        const isUpdate = $("#btn-submit-rawatDarurat").text() === 'Update Rawat Darurat';

        if (isUpdate) {
            this.updateRawatDarurat();
        } else {
            this.submitRawatDarurat();
        }
    },

    submitRawatDarurat: function() {
        const id = $("#id-rawatDarurat").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.POST_RAWAT_DARURAT, {id: id});
        const self = this;

        const formData = {
            _token: PendaftaranConfig.csrf,
            jenis_jaminan_rawatDarurat: $("#jenis_jaminan_rawatDarurat").val(),
            dokter_rawatDarurat: $("#dokter_rawatDarurat").val(),
            tingkat_kegawatan: $("#tingkat_kegawatan").val(),
            keluhan_utama: $("#keluhan_utama").val()
        };

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                self.handleEncounterResponse(res, 'rawatDarurat');
            }
        });
    },

    updateRawatDarurat: function() {
        const id = $("#id-rawatDarurat").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.UPDATE_RAWAT_DARURAT, {id: id});
        const self = this;

        const formData = {
            _token: PendaftaranConfig.csrf,
            jenis_jaminan_rawatDarurat: $("#jenis_jaminan_rawatDarurat").val(),
            dokter_rawatDarurat: $("#dokter_rawatDarurat").val(),
            tingkat_kegawatan: $("#tingkat_kegawatan").val(),
            keluhan_utama: $("#keluhan_utama").val()
        };

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                self.handleEncounterResponse(res, 'rawatDarurat');
            }
        });
    },

    // Helper Methods
    loadPatientData: function(id, type) {
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.SHOW_PASIEN, {id: id});
        const self = this;

        PendaftaranHelpers.ajax(url, {
            success: function(res) {
                self.fillPatientInfo(res.data, type);
                $(`#id-${type}`).val(id);
            }
        });
    },

    fillPatientInfo: function(data, type) {
        $(`#no_rm_${type}`).text(data.rekam_medis || '-');
        $(`#name_${type}`).text(data.name || data.name_pasien || '-');
        $(`#tgl_lahir_${type}`).text(data.umur || '-');

        if (type === 'rawatJalan') {
            $("#status-pasien").text(data.status || '-');
        } else {
            $(`#status-pasien-${type}`).text(data.status || '-');
        }

        $(`#no_encounter_${type}`).text(data.no_encounter || '-');
        $(`#created_${type}`).text(data.tgl_encounter || '-');
        $(`#type_${type}`).text(data.type || '-');
    },

    resetForm: function(type) {
        PendaftaranHelpers.hideFormErrors(`#error-${type}`);

        if (type === 'rawatJalan') {
            $("#jenis_jaminan").val("");
            $("#dokter").val("");
            $("#tujuan_kunjungan").val("");
        } else if (type === 'rawatInap') {
            $("#jenis_jaminan_rawatInap").val("");
            $("#dokter_rawatInap").val("");
            $("#ruangan_rawatInap").val("");
            $("#tujuan_kunjungan_rawatInap").val("");
        } else if (type === 'rawatDarurat') {
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#dokter_rawatDarurat").val("");
            $("#tingkat_kegawatan").val("");
            $("#keluhan_utama").val("");
        }
    },

    handleEncounterResponse: function(res, type) {
        if ($.isEmptyObject(res.error)) {
            PendaftaranHelpers.hideFormErrors(`#error-${type}`);
            PendaftaranHelpers.showAlert(res.text, res.status ? 'success' : 'error');

            if (res.status) {
                // Refresh appropriate tab
                if (type === 'rawatJalan') {
                    PendaftaranTabs.loadRawatJalan();
                } else if (type === 'rawatInap') {
                    PendaftaranTabs.loadRawatInap();
                } else if (type === 'rawatDarurat') {
                    PendaftaranTabs.loadRawatDarurat();
                }

                $(`#btn-tutup-${type}`).click();
            }
        } else {
            PendaftaranHelpers.showFormErrors(res.error, `#error-${type}`);
        }
    }
};
