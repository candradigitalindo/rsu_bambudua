<div class="sidebarMenuScroll">
    <ul class="sidebar-menu">
        <li class="{{ request()->is('home*') ? 'active current-page' : '' }}">
            <a href="{{ route('home') }}">
                <i class="ri-home-6-line"></i>
                <span class="menu-text">Hospital Admin</span>
            </a>
        </li>
        <li class="{{ request()->is('pendaftaran*') ? 'active current-page' : '' }}">
            <a href="{{ route('pendaftaran.index') }}">
                <i class="ri-draft-line"></i>
                <span class="menu-text">Pendaftaran</span>
            </a>
        </li>

        <li class="treeview {{ request()->is('masterdata*') ? 'active current-page' : '' }}">
            <a href="#">
                <i class="ri-archive-drawer-line"></i>
                <span class="menu-text">Master Data</span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a class="{{ request()->is('masterdata/wilayah*') ? 'active-sub' : '' }}" href="{{ route('wilayah.index') }}">Data Wilayah</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/jenisjaminan*') ? 'active-sub' : '' }}" href="{{ route('jenisjaminan.index') }}">Jenis Jaminan</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/etnis*') ? 'active-sub' : '' }}" href="{{ route('etnis.index') }}">Data Etnis</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/pendidikan*') ? 'active-sub' : '' }}" href="{{ route('pendidikan.index') }}">Data Pendidikan</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/agama*') ? 'active-sub' : '' }}" href="{{ route('agama.index') }}">Data Agama</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/pekerjaan*') ? 'active-sub' : '' }}" href="{{ route('pekerjaan.index') }}">Data Pekerjaan</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/spesialis*') ? 'active-sub' : '' }}" href="{{ route('spesialis.index') }}">Data Spesialis</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/ruangan*') || request()->is('masterdata/category*') ? 'active-sub' : '' }}" href="{{ route('ruangan.index') }}">Data Ruangan</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/tindakan*') ? 'active-sub' : '' }}" href="{{ route('tindakan.index') }}">Data Tindakan</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/icd10*') ? 'active-sub' : '' }}" href="{{ route('icd10.index') }}">Data ICD10</a>
                </li>
                <li>
                    <a class="{{ request()->is('masterdata/discounts*') ? 'active-sub' : '' }}" href="{{ route('discounts.index') }}">Data Diskon</a>
                </li>

            </ul>
        </li>
        <li class="treeview {{ request()->is('kunjungan*') ? 'active current-page' : '' }}">
            <a href="#">
                <i class="ri-dossier-line"></i>
                <span class="menu-text">Data Kunjungan</span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a class="{{ request()->is('kunjungan/rawatJalan*') || request()->is('kunjungan/observasi*') ? 'active-sub' : '' }}" href="{{ route('kunjungan.rawatJalan') }}">Rawat Jalan</a>
                </li>
                <li>
                    <a class="{{ request()->is('kunjungan/rawatInap*') ? 'active-sub' : '' }}" href="{{ route('kunjungan.rawatInap') }}">Rawat Inap</a>
                </li>
                <li>
                    <a class="{{ request()->is('kunjungan/rawatDarurat*') ? 'active-sub' : '' }}" href="{{ route('kunjungan.rawatDarurat') }}">IGD</a>
                </li>

            </ul>
        </li>
        <li class="treeview {{ request()->is('apotek*') ? 'active current-page' : '' }}">
            <a href="#">
                <i class="ri-medicine-bottle-line"></i>
                <span class="menu-text">Apotek</span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a class="{{ request()->is('apotek/dashboard*') ? 'active-sub' : '' }}" href="{{ route('apotek.dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a class="{{ request()->is('apotek/categories*') ? 'active-sub' : '' }}" href="{{ route('categories.index') }}">Kategori</a>
                </li>
                <li>
                    <a class="{{ request()->is('apotek/products*') ? 'active-sub' : '' }}" href="{{ route('products.index') }}">Produk</a>
                </li>
                <li>
                    <a class="{{ request()->is('apotek/encounter*') ? 'active-sub' : '' }}" href="{{ route('apotek.getEncounter') }}">Resep Dokter</a>
                </li>
            </ul>
        </li>
        <li class="{{ request()->is('bahans*') ? 'active current-page' : '' }}">
            <a href="{{ route('bahans.index') }}">
                <i class="ri-archive-line"></i>
                <span class="menu-text">Data Stok Perlengkapan</span>
            </a>
        </li>
        <li class="{{ request()->is('pengguna*') ? 'active current-page' : '' }}">
            <a href="{{ route('pengguna.index') }}">
                <i class="ri-team-line"></i>
                <span class="menu-text">Pengguna</span>
            </a>
        </li>
        <li class="treeview {{ request()->is('setting*') ? 'active current-page' : '' }}">
            <a href="#">
                <i class="ri-settings-5-line"></i>
                <span class="menu-text">Setting</span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a class="{{ request()->is('setting/satusehat*') ? 'active-sub' : '' }}" href="{{ route('satusehat.index') }}">Satusehat</a>
                </li>
                <li>
                    <a class="{{ request()->is('setting/lokasiloket*') || request()->is('setting/loket*') ? 'active-sub' : '' }}" href="{{ route('lokasiloket.index') }}">Loket Antrian</a>
                </li>

            </ul>
        </li>
    </ul>
</div>
