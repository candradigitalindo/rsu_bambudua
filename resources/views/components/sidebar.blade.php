<!-- Sidebar wrapper starts -->
<nav id="sidebar" class="sidebar-wrapper" style="height:100vh;display:flex;flex-direction:column;">

    <!-- Brand container starts -->
    <div class="brand-container d-flex align-items-center justify-content-between">
        <div class="app-brand ms-3">
            <a href="/">
                <img src="{{ $logo ?? asset('images/bdc.png') }}" class="logo" alt="Logo">
            </a>
        </div>
        <button type="button" class="pin-sidebar me-3">
            <i class="ri-menu-line"></i>
        </button>
    </div>
    <!-- Brand container ends -->

    <!-- Sidebar menu starts -->
    <div class="sidebarMenuScroll" style="flex:1 1 auto;min-height:0;overflow-y:auto;padding-bottom:0;margin-bottom:0;">
        <ul class="sidebar-menu" style="margin-bottom:0;padding-bottom:0;">
            @if (auth()->user()->role == 1)
                <li class="{{ request()->is('home*') ? 'active current-page' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="ri-home-6-line"></i>
                        <span class="menu-text">Dashboard Owner</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role == 4)
                {{-- Admin Dashboard --}}
                <li class="{{ request()->is('admin/dashboard*') ? 'active current-page' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="ri-shield-user-line"></i>
                        <span class="menu-text">Dashboard Admin</span>
                    </a>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [5, 4, 1]))
                <li
                    class="treeview {{ request()->is('pendaftaran*') || request()->is('loket*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-draft-line"></i>
                        <span class="menu-text">Loket</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('loket/dashboard*') ? 'active-sub' : '' }}"
                                href="{{ route('loket.dashboard') }}">Dashboard</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('pendaftaran*') ? 'active-sub' : '' }}"
                                href="{{ route('pendaftaran.index') }}">Pendaftaran</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('loket/reminder*') ? 'active-sub' : '' }}"
                                href="{{ route('loket.getReminderEncounter') }}"><span class="menu-text">Reminder
                                    Pasien</span>
                                @if (!empty($reminderCount) && $reminderCount > 0)
                                    <span class="badge bg-primary ms-auto">{{ $reminderCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [8, 4, 1]))
                <li class="treeview {{ request()->is('laboratorium*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-test-tube-line"></i>
                        <span class="menu-text">Laboratorium</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('laboratorium/dashboard*') ? 'active-sub' : '' }}"
                                href="{{ route('lab.dashboard') }}">Dashboard Lab</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('laboratorium/requests*') ? 'active-sub' : '' }}"
                                href="{{ route('lab.requests.index') }}">Permintaan Pemeriksaan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('laboratorium/results*') ? 'active-sub' : '' }}"
                                href="{{ route('lab.results.index') }}">Input Hasil Lab</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('laboratorium/reagents*') ? 'active-sub' : '' }}"
                                href="{{ route('lab.reagents.index') }}">Stok Reagensia</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [9, 4, 1]))
                <li class="treeview {{ request()->is('radiologi*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-flask-line"></i>
                        <span class="menu-text">Radiologi</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('radiologi/dashboard*') ? 'active-sub' : '' }}"
                                href="{{ route('radiologi.dashboard') }}">Dashboard Radiologi</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('radiologi/permintaan*') ? 'active-sub' : '' }}"
                                href="{{ route('radiologi.requests.index') }}">Permintaan Radiologi</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('radiologi/hasil*') ? 'active-sub' : '' }}"
                                href="{{ route('radiologi.results.index') }}">Hasil Radiologi</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('radiologi/jadwal*') ? 'active-sub' : '' }}"
                                href="{{ route('radiologi.schedule.index') }}">Jadwal Pemeriksaan</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [1, 4]))
                {{-- Admin & Owner --}}
                <li class="treeview {{ request()->is('masterdata*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-archive-drawer-line"></i>
                        <span class="menu-text">Master Data</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('masterdata/wilayah*') ? 'active-sub' : '' }}"
                                href="{{ route('wilayah.index') }}">Data Wilayah</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/jenisjaminan*') ? 'active-sub' : '' }}"
                                href="{{ route('jenisjaminan.index') }}">Jenis Jaminan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/etnis*') ? 'active-sub' : '' }}"
                                href="{{ route('etnis.index') }}">Data Etnis</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/pendidikan*') ? 'active-sub' : '' }}"
                                href="{{ route('pendidikan.index') }}">Data Pendidikan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/agama*') ? 'active-sub' : '' }}"
                                href="{{ route('agama.index') }}">Data Agama</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/pekerjaan*') ? 'active-sub' : '' }}"
                                href="{{ route('pekerjaan.index') }}">Data Pekerjaan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/spesialis*') ? 'active-sub' : '' }}"
                                href="{{ route('spesialis.index') }}">Data Spesialis</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/ruangan*') || request()->is('masterdata/category*') ? 'active-sub' : '' }}"
                                href="{{ route('ruangan.index') }}">Data Ruangan</a>
                        </li>

                        <li>
                            <a class="{{ request()->is('masterdata/tindakan*') ? 'active-sub' : '' }}"
                                href="{{ route('tindakan.index') }}">Data Tindakan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/jenis-pemeriksaan*') ? 'active-sub' : '' }}"
                                href="{{ route('jenis-pemeriksaan.index') }}">Pemeriksaan Penunjang</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/icd10*') ? 'active-sub' : '' }}"
                                href="{{ route('icd10.index') }}">Data ICD10</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/discount*') ? 'active-sub' : '' }}"
                                href="{{ route('discounts.index') }}">Data Diskon</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/clinics*') ? 'active-sub' : '' }}"
                                href="{{ route('clinics.index') }}">Data Poliklinik</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('masterdata/units*') ? 'active-sub' : '' }}"
                                href="{{ route('units.index') }}">Data Satuan</a>
                        </li>

                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [2, 3, 4, 1]))
                <li class="treeview {{ request()->is('kunjungan*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-dossier-line"></i>
                        <span class="menu-text">Layanan Medis</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('kunjungan/dashboard-dokter*') ? 'active-sub' : '' }}"
                                href="{{ route('dokter.index') }}">Dashboard</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('kunjungan/rawatJalan*') ? 'active-sub' : '' }}"
                                href="{{ route('kunjungan.rawatJalan') }}">Rawat Jalan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('kunjungan/rawatInap*') ? 'active-sub' : '' }}"
                                href="{{ route('kunjungan.rawatInap') }}">Rawat Inap</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('kunjungan/rawatDarurat*') ? 'active-sub' : '' }}"
                                href="{{ route('kunjungan.rawatDarurat') }}">IGD</a>
                        </li>
                        @if (in_array(auth()->user()->role, [3, 4, 1]))
                            <li>
                                <a class="{{ request()->is('kunjungan/dashboard-bed-perawat') ? 'active-sub' : '' }}"
                                    href="{{ route('kunjungan.nurse-bed-dashboard') }}">
                                    Dashboard Bed
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role == 1)
                {{-- Owner Only --}}
                <li class="treeview {{ request()->is('medical-records*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-file-list-3-line"></i>
                        <span class="menu-text">Rekam Medis</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('medical-records/dashboard*') ? 'active-sub' : '' }}"
                                href="{{ route('medical-records.dashboard') }}">Dashboard</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('medical-records/riwayat*') ? 'active-sub' : '' }}"
                                href="{{ route('medical-records.riwayat') }}">Riwayat Kunjungan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('medical-records/arsip*') ? 'active-sub' : '' }}"
                                href="{{ route('medical-records.arsip') }}">Arsip</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('medical-records/statistik*') ? 'active-sub' : '' }}"
                                href="{{ route('medical-records.statistik') }}">Statistik</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [6, 1]))
                {{-- Keuangan & Owner Only --}}
                <li class="treeview {{ request()->is('keuangan*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-funds-line"></i>
                        <span class="menu-text">Keuangan</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('keuangan/dashboard*') ? 'active-sub' : '' }}"
                                href="{{ route('keuangan.index') }}">Laporan Keuangan</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('keuangan/gaji*') ? 'active-sub' : '' }}"
                                href="{{ route('keuangan.gaji') }}">Gaji & Insentif</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('keuangan/pengaturan-insentif*') ? 'active-sub' : '' }}"
                                href="{{ route('keuangan.incentive.settings') }}">Pengaturan Insentif</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('keuangan/insentif-manual*') ? 'active-sub' : '' }}"
                                href="{{ route('keuangan.insentif.index') }}">Manajemen Insentif</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('keuangan/operasional*') ? 'active-sub' : '' }}"
                                href="{{ route('operasional.index') }}">Pengeluaran Operasional</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('keuangan/pendapatan-lain*') ? 'active-sub' : '' }}"
                                href="{{ route('pendapatan-lain.index') }}">Pendapatan Lainnya</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [10, 1]))
                {{-- Kasir & Owner Only --}}
                <li class="treeview {{ request()->is('kasir*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-bank-card-line"></i>
                        <span class="menu-text">Kasir</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('kasir*') ? 'active-sub' : '' }}"
                                href="{{ route('kasir.index') }}">Transaksi Pembayaran</a>
                        </li>

                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [7, 4, 1]))
                {{-- Apotek, Admin, Owner --}}
                <li class="treeview {{ request()->is('apotek*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-medicine-bottle-line"></i>
                        <span class="menu-text">Apotek</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('apotek/dashboard*') ? 'active-sub' : '' }}"
                                href="{{ route('apotek.dashboard') }}">Dashboard</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('apotek/categories*') ? 'active-sub' : '' }}"
                                href="{{ route('categories.index') }}">Kategori</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('apotek/products*') ? 'active-sub' : '' }}"
                                href="{{ route('products.index') }}">Produk</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('apotek/penyiapan-resep*') ? 'active-sub' : '' }}"
                                href="{{ route('apotek.penyiapan-resep') }}">Penyiapan Resep</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('apotek/permintaan-inap*') ? 'active-sub' : '' }}"
                                href="{{ route('apotek.permintaan-inap') }}">Permintaan Obat Inap</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('apotek/penyiapan-resep/reorder-list*') ? 'active-sub' : '' }}"
                                href="{{ route('apotek.penyiapan-resep.reorder.list') }}">Siapkan Ulang Resep</a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (in_array(auth()->user()->role, [1, 4])) {{-- Admin & Owner --}}
                <li class="{{ request()->is('bahans*') ? 'active current-page' : '' }}">
                    <a href="{{ route('bahans.index') }}">
                        <i class="ri-archive-line"></i>
                        <span class="menu-text">Data Stok Perlengkapan</span>
                    </a>
                </li>
                <li class="treeview {{ request()->is('pengguna*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-user-settings-line"></i>
                        <span class="menu-text">Manajemen Pengguna</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('pengguna') ? 'active-sub' : '' }}"
                                href="{{ route('pengguna.index') }}">Data Pengguna</a>
                        </li>
                        <li>
                            @if (Route::has('pengguna.activity.index'))
                                <a class="{{ request()->is('pengguna/aktivitas*') ? 'active-sub' : '' }}"
                                    href="{{ route('pengguna.activity.index') }}">Aktifitas Pengguna</a>
                            @else
                                <a class="disabled" href="#"
                                    title="Route pengguna.activity.index belum tersedia">Aktifitas Pengguna</a>
                            @endif
                        </li>
                    </ul>
                </li>
                <li class="treeview {{ request()->is('setting*') ? 'active current-page' : '' }}">
                    <a href="#">
                        <i class="ri-settings-5-line"></i>
                        <span class="menu-text">Setting</span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a class="{{ request()->is('setting/satusehat*') ? 'active-sub' : '' }}"
                                href="{{ route('satusehat.index') }}">Satusehat</a>
                        </li>
                        <li>
                            <a class="{{ request()->is('setting/lokasiloket*') || request()->is('setting/loket*') ? 'active-sub' : '' }}"
                                href="{{ route('lokasiloket.index') }}">Loket Antrian</a>
                        </li>

                    </ul>
                </li>
                <li class="{{ request()->is('berita*') ? 'active current-page' : '' }}">
                    <a href="{{ route('berita.index') }}">
                        <i class="ri-newspaper-line"></i>
                        <span class="menu-text">Berita Terbaru</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
    <!-- Sidebar menu ends -->

</nav>
<!-- Sidebar wrapper ends -->

@push('style')
    <style>
        html,
        body {
            height: 100%;
        }

        .sidebar-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebarMenuScroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
            background: #fff;
            /* opsional, agar tidak transparan */
        }

        .sidebar-menu {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }
    </style>
@endpush
