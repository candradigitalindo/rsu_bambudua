<div class="sidebarMenuScroll">
    <ul class="sidebar-menu">
        <li class="{{ request()->is('home*') ? 'active current-page' : '' }}">
            <a href="{{ route('home') }}">
                <i class="ri-home-6-line"></i>
                <span class="menu-text">Hospital Admin</span>
            </a>
        </li>
        <li class="{{ request()->is('wilayah*') ? 'active current-page' : '' }}">
            <a href="{{ route('wilayah.index') }}">
                <i class="ri-map-pin-2-fill"></i>
                <span class="menu-text">Master Wilayah</span>
            </a>
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
