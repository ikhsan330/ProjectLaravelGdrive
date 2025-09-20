<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
      <div class="me-3">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
      </div>
      <div>
        @auth
            @if(Auth::user()->role == 'admin')
            <a class="navbar-brand brand-logo" href="{{ route('admin.dashboard') }}">
            @elseif(Auth::user()->role == 'dosen')
            <a class="navbar-brand brand-logo" href="{{ route('dosen.dashboard') }}">
            @elseif(Auth::user()->role == 'kaprodi')
            <a class="navbar-brand brand-logo" href="{{ route('kaprodi.dashboard') }}">
            @endif
        @endauth
          <img src="{{ asset('images/logoArsipDokumen2.png') }}" alt="logo" style="width:120px; height:40px;" />
        </a>
        @auth
            @if(Auth::user()->role == 'admin')
            <a class="navbar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
            @elseif(Auth::user()->role == 'dosen')
            <a class="navbar-brand brand-logo-mini" href="{{ route('dosen.dashboard') }}">
            @elseif(Auth::user()->role == 'kaprodi')
            <a class="navbar-brand brand-logo-mini" href="{{ route('kaprodi.dashboard') }}">
            @endif
        @endauth
          <img src="{{ asset('images/favicon.ico') }}" alt="logo" />
        </a>
      </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top">
      <ul class="navbar-nav">
       @auth
    <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        {{-- Menampilkan nama pengguna yang sedang login --}}
        <h1 class="welcome-text">Selamat Pagi, <span class="text-black fw-bold">{{ Auth::user()->name }}</span></h1>

        {{-- Menampilkan pesan sesuai role --}}
        @if(Auth::user()->role == 'admin')
            <h3 class="welcome-sub-text">Anda login sebagai Admin. Selamat Bekerja.</h3>
        @elseif(Auth::user()->role == 'dosen')
            <h3 class="welcome-sub-text">Semangat menginspirasi, Bapak/Ibu Dosen.</h3>
        @elseif(Auth::user()->role == 'kaprodi')
            <h3 class="welcome-sub-text">Selamat datang, Kepala Program Studi.</h3>
        @else
            <h3 class="welcome-sub-text">Selamat datang di sistem kami.</h3>
        @endif
    </li>
@endauth
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item d-none d-lg-block">
          <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
            <span class="input-group-addon input-group-prepend border-right">
              <span class="icon-calendar input-group-text calendar-icon"></span>
            </span>
            <input type="text" class="form-control">
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="icon-bell"></i>
            <span class="count"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
            <a class="dropdown-item py-3">
              <p class="mb-0 font-weight-medium float-left">You have 7 unread mails </p>
              <span class="badge badge-pill badge-primary float-right">View all</span>
            </a>
            </div>
        </li>
        @auth
<li class="nav-item dropdown d-none d-lg-block user-dropdown">
    <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">

      <img class="img-xs rounded-circle" src="{{ asset('images/faces/face8.jpg') }}" alt="Profile image">
    </a>
    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
        <div class="dropdown-header text-center">

            <img class="img-md rounded-circle" src="{{ asset('images/faces/face8.jpg') }}" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->name }}</p>
            <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
        </div>


        <a class="dropdown-item" href="{{ route('profile.edit') }}">
            <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a class="dropdown-item" href="{{ route('logout') }}"
               onclick="event.preventDefault(); this.closest('form').submit();">
                <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out
            </a>
        </form>
    </div>
</li>
@endauth
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
</nav>
