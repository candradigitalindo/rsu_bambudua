window.PendaftaranAntrian = {
    init: function() {
        this.bindEvents();
    },

    bindEvents: function() {
        $(PendaftaranConfig.elements.btnNext).on("click", this.handleNext.bind(this));
    },

    handleNext: function() {
        PendaftaranHelpers.ajax(PendaftaranConfig.routes.UPDATE_ANTRIAN, {
            type: 'POST',
            success: function(res) {
                if (res.status) {
                    // Update UI
                    $(PendaftaranConfig.elements.antrian).text(res.antrian.prefix + " " + res.antrian.nomor);
                    $(PendaftaranConfig.elements.jumlah).text(res.jumlah);

                    // Play voice notification
                    if (typeof responsiveVoice !== 'undefined') {
                        responsiveVoice.speak(
                            `Nomor Antrian ${res.antrian.prefix}-${res.antrian.nomor}, ke loket ${res.loket.kode_loket}`,
                            "Indonesian Female",
                            { rate: 0.9, pitch: 1, volume: 1 }
                        );
                    }

                    PendaftaranHelpers.showAlert('Antrian berhasil diupdate', 'success');
                }
            }
        });
    }
};
