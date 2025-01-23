<div class="sidebarMenuScroll">
    <ul class="sidebar-menu">
        <li class="{{ request()->is('home*') ? 'active current-page' : '' }}">
            <a href="{{ route('home') }}">
                <i class="ri-home-6-line"></i>
                <span class="menu-text">Hospital Admin</span>
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

            </ul>
        </li>
        <li class="treeview {{ request()->is('setting*') ? 'active current-page' : '' }}">
            <a href="#">
                <i class="ri-settings-5-line"></i>
                <span class="menu-text">Setting</span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a class="{{ request()->is('setting*') ? 'active-sub' : '' }}" href="{{ route('satusehat.index') }}">Satusehat</a>
                </li>

            </ul>
        </li>

    </ul>
</div>
