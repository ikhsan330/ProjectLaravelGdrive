<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        @auth

            <li class="nav-item {{ request()->routeIs('*.dashboard.*') ? 'active' : '' }}">
                <a class="nav-link"
                    @if(Auth::user()->role == 'admin')
                        href="{{ route('admin.dashboard') }}"
                    @elseif(Auth::user()->role == 'dosen')
                        href="{{ route('dosen.dashboard') }}"
                    @elseif(Auth::user()->role == 'kaprodi')
                        href="{{ route('kaprodi.dashboard') }}"
                    @elseif(Auth::user()->role == 'asesor')
                        href="{{ route('asesor.dashboard') }}"
                    @elseif(Auth::user()->role == 'p4pm')
                        href="{{ route('p4pm.dashboard') }}"
                    @endif
                >
                    <i class="mdi mdi-home menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item nav-category">Menu</li>


            <li class="nav-item {{ request()->routeIs('*.dokumen.*') ? 'active' : '' }}">
                <a class="nav-link"
                    @if(Auth::user()->role == 'admin')
                        href="{{ route('admin.dokumen.index') }}"
                    @elseif(Auth::user()->role == 'dosen')
                        href="{{ route('dosen.dokumen.index') }}"
                    @elseif(Auth::user()->role == 'kaprodi')
                        href="{{ route('kaprodi.dokumen.index') }}"
                    @elseif(Auth::user()->role == 'asesor')
                        href="{{ route('assesor.dokumen.index') }}"
                    @elseif(Auth::user()->role == 'p4pm')
                        href="{{ route('p4pm.dokumen.index') }}"
                    @endif
                >
                    <i class="menu-icon mdi mdi-file-document"></i>
                    <span class="menu-title">Document</span>
                </a>
            </li>


         @if(Auth::user()->role == 'admin')
                {{-- Menu Manajemen User (sebagai referensi) --}}
                <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="menu-icon mdi mdi-account-multiple-plus"></i>
                        <span class="menu-title">Manajemen User</span>
                    </a>
                </li>

                {{-- BARU: Menu Pengaturan Google Drive --}}
                <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.settings.google.edit') }}">
                        <i class="menu-icon mdi mdi-settings"></i>
                        <span class="menu-title">Pengaturan Drive</span>
                    </a>
                </li>
            @endif

        @endauth
    </ul>
</nav>
