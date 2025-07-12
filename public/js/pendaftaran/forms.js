window.PendaftaranForms = {
    init: function() {
        this.bindEvents();
        this.bindAdditionalEvents(); // Add this line
    },

    bindEvents: function() {
        // Patient Form Events
        $("#btn-buatPasienBaru").on("click", this.resetPatientForm.bind(this));
        $("#btn-simpan").on("click", this.submitPatientForm.bind(this));
        $("#btn-edit").on("click", this.updatePatientForm.bind(this));

        // Province Change Events
        $("#province, #province_edit").on("change", this.handleProvinceChange.bind(this));

        // Edit Patient Event
        $(document).on('click', '.edit', this.handleEditPatient.bind(this));
    },

    bindAdditionalEvents: function() {
        // BMI Calculator
        $(document).on("input", "#tinggi_badan, #berat_badan, #tinggi_badan_edit, #berat_badan_edit", this.calculateBMI.bind(this));

        // Address toggle
        $(document).on("change", "#sama_dengan_ktp, #sama_dengan_ktp_edit", this.toggleDomisiliAddress.bind(this));

        // Phone number formatting
        $(document).on("input", "#no_hp, #no_hp_edit", this.formatPhoneNumber.bind(this));

        // Province change for domisili
        $(document).on("change", "#province_domisili, #province_domisili_edit", this.handleDomisiliProvinceChange.bind(this));

        // Auto-calculate age from birth date
        $(document).on("change", "#tgl_lahir, #tgl_lahir_edit", this.calculateAge.bind(this));
    },

    resetPatientForm: function() {
        PendaftaranHelpers.resetForm(PendaftaranConfig.elements.formPasien);
        PendaftaranHelpers.hideFormErrors("#error");

        // Reset to default values
        $("#kewarganegaraan").val(1);
        $("#metode_komunikasi").val(1);
        $("#negara").val("Indonesia");
        $("#sama_dengan_ktp").prop('checked', false);
        $("#alamat_domisili_section").hide();

        // Clear validation classes
        $(PendaftaranConfig.elements.formPasien).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    },

    submitPatientForm: function() {
        // Validate form first
        if (!this.validateForm(PendaftaranConfig.elements.formPasien)) {
            PendaftaranHelpers.showAlert('Mohon lengkapi semua field yang wajib diisi', 'error');
            return;
        }

        const formData = $(PendaftaranConfig.elements.formPasien).serialize();

        PendaftaranHelpers.ajax(PendaftaranConfig.routes.STORE_PASIEN, {
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $("#btn-simpan").prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            },
            complete: function() {
                $("#btn-simpan").prop('disabled', false).html('Simpan');
            },
            success: function(res) {
                if ($.isEmptyObject(res.error)) {
                    PendaftaranHelpers.hideFormErrors("#error");
                    PendaftaranHelpers.showAlert(res.text, res.status ? 'success' : 'error');

                    if (res.status) {
                        PendaftaranForms.resetPatientForm();
                        $("#btn-kembali").click();
                    }
                } else {
                    PendaftaranHelpers.showFormErrors(res.error, "#error");
                }
            }
        });
    },

    updatePatientForm: function() {
        const id = $("#id").val();
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.UPDATE_PASIEN, {id: id});
        const formData = $(PendaftaranConfig.elements.formEditPasien).serialize();

        PendaftaranHelpers.ajax(url, {
            type: 'POST',
            data: formData,
            success: function(res) {
                if ($.isEmptyObject(res.error)) {
                    PendaftaranHelpers.hideFormErrors("#error-edit");
                    PendaftaranHelpers.showAlert(res.text, res.status ? 'success' : 'error');

                    if (res.status) {
                        $("#btn-edit-kembali").click();
                    }
                } else {
                    PendaftaranHelpers.showFormErrors(res.error, "#error-edit");
                }
            }
        });
    },

    handleEditPatient: function(e) {
        const id = $(e.target).attr('id');
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.EDIT_PASIEN, {id: id});

        PendaftaranHelpers.hideFormErrors("#error-edit");

        PendaftaranHelpers.ajax(url, {
            success: function(res) {
                // Fill form with patient data
                $("#jenis_identitas_edit").val(res.data.jenis_identitas);
                $("#no_identitas_edit").val(res.data.no_identitas);
                $("#name_pasien_edit").val(res.data.name);
                $("#jenis_kelamin_edit").val(res.data.jenis_kelamin);
                $("#tgl_lahir_edit").val(res.data.tgl_lahir);
                $("#golongan_darah_edit").val(res.data.golongan_darah);
                $("#kewarganegaraan_edit").val(res.data.kewarganegaraan);
                $("#pekerjaan_edit").val(res.data.pekerjaan);
                $("#status_menikah_edit").val(res.data.status_menikah);
                $("#agama_edit").val(res.data.agama);
                $("#no_hp_edit").val(res.data.no_hp);
                $("#no_telepon_edit").val(res.data.no_telepon);
                $("#alamat_edit").val(res.data.alamat);
                $("#province_edit").val(res.data.province_code);
                $("#id").val(res.data.id);

                // Load cities
                PendaftaranForms.loadCities(res.data.province_code, res.data.city_code, '#city_edit');
            }
        });
    },

    handleProvinceChange: function(e) {
        const provinceId = $(e.target).val();
        const isEdit = $(e.target).attr('id') === 'province_edit';
        const citySelector = isEdit ? '#city_edit' : '#city';

        if (provinceId) {
            this.loadCities(provinceId, null, citySelector);
        } else {
            $(citySelector).html('<option value="">-- Pilih Provinsi dulu --</option>');
        }
    },

    loadCities: function(provinceCode, selectedCity = null, targetSelector) {
        const url = PendaftaranHelpers.buildUrl(PendaftaranConfig.routes.WILAYAH_CITY, {code: provinceCode});

        PendaftaranHelpers.ajax(url, {
            success: function(cities) {
                let options = '<option value="">Pilih Kota/Kabupaten</option>';

                cities.forEach(function(city) {
                    const selected = selectedCity && selectedCity === city.code ? 'selected' : '';
                    options += `<option value="${city.code}" ${selected}>${city.name}</option>`;
                });

                $(targetSelector).html(options);
            },
            error: function() {
                $(targetSelector).html('<option value="">Error loading cities</option>');
            }
        });
    },

    calculateBMI: function(e) {
        const isEdit = $(e.target).attr('id').includes('_edit');
        const suffix = isEdit ? '_edit' : '';

        const tinggi = parseFloat($(`#tinggi_badan${suffix}`).val());
        const berat = parseFloat($(`#berat_badan${suffix}`).val());

        if (tinggi && berat && tinggi > 0) {
            const tinggiMeter = tinggi / 100;
            const imt = (berat / (tinggiMeter * tinggiMeter)).toFixed(1);

            $(`#imt${suffix}`).val(imt);

            // Determine BMI category
            let kategori = '';
            let colorClass = '';

            if (imt < 18.5) {
                kategori = 'Kurus (Underweight)';
                colorClass = 'text-info';
            } else if (imt < 25) {
                kategori = 'Normal (Ideal)';
                colorClass = 'text-success';
            } else if (imt < 30) {
                kategori = 'Gemuk (Overweight)';
                colorClass = 'text-warning';
            } else {
                kategori = 'Obesitas';
                colorClass = 'text-danger';
            }

            $(`#kategori_imt${suffix}`).val(kategori).removeClass('text-info text-success text-warning text-danger').addClass(colorClass);
        }
    },

    calculateAge: function(e) {
        const isEdit = $(e.target).attr('id').includes('_edit');
        const suffix = isEdit ? '_edit' : '';
        const birthDate = new Date($(e.target).val());

        if (birthDate) {
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            // You can display age somewhere if needed
            console.log(`Umur: ${age} tahun`);
        }
    },

    toggleDomisiliAddress: function(e) {
        const isEdit = $(e.target).attr('id').includes('_edit');
        const suffix = isEdit ? '_edit' : '';

        if ($(e.target).is(':checked')) {
            $(`#alamat_domisili_section${suffix}`).slideUp();
            // Copy KTP address to domisili
            this.copyKTPToDomisili(isEdit);
        } else {
            $(`#alamat_domisili_section${suffix}`).slideDown();
        }
    },

    copyKTPToDomisili: function(isEdit) {
        const suffix = isEdit ? '_edit' : '';

        // Copy address data
        const alamat = $(`#alamat${suffix}`).val();
        const province = $(`#province${suffix}`).val();
        const city = $(`#city${suffix}`).val();
        const kodePos = $(`#kode_pos${suffix}`).val();

        $(`#alamat_domisili${suffix}`).val(alamat);
        $(`#province_domisili${suffix}`).val(province);
        $(`#kode_pos_domisili${suffix}`).val(kodePos);

        // Load cities for domisili if province is selected
        if (province) {
            this.loadCities(province, city, `#city_domisili${suffix}`);
        }
    },

    formatPhoneNumber: function(e) {
        let value = $(e.target).val();

        // Remove non-numeric characters
        value = value.replace(/\D/g, '');

        // Remove leading zero if present
        if (value.startsWith('0')) {
            value = value.substring(1);
        }

        // Limit to 13 characters
        if (value.length > 13) {
            value = value.substring(0, 13);
        }

        $(e.target).val(value);

        // Add validation visual feedback
        if (value.length >= 9 && value.length <= 13) {
            $(e.target).removeClass('is-invalid').addClass('is-valid');
        } else if (value.length > 0) {
            $(e.target).removeClass('is-valid').addClass('is-invalid');
        } else {
            $(e.target).removeClass('is-valid is-invalid');
        }
    },

    handleDomisiliProvinceChange: function(e) {
        const provinceId = $(e.target).val();
        const isEdit = $(e.target).attr('id') === 'province_domisili_edit';
        const citySelector = isEdit ? '#city_domisili_edit' : '#city_domisili';

        if (provinceId) {
            this.loadCities(provinceId, null, citySelector);
        } else {
            $(citySelector).html('<option value="">-- Pilih Provinsi dulu --</option>');
        }
    },

    // Enhanced form validation
    validateForm: function(formId) {
        const form = $(formId);
        let isValid = true;

        // Required fields validation
        form.find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Phone number validation
        const phoneField = form.find('#no_hp, #no_hp_edit');
        if (phoneField.length) {
            const phoneValue = phoneField.val();
            if (phoneValue && (phoneValue.length < 9 || phoneValue.length > 13)) {
                phoneField.addClass('is-invalid');
                isValid = false;
            }
        }

        // Email validation
        const emailField = form.find('#email, #email_edit');
        if (emailField.length && emailField.val()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.val())) {
                emailField.addClass('is-invalid');
                isValid = false;
            }
        }

        return isValid;
    },
};
