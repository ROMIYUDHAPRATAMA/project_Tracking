<header class="topbar" id="topbar">
  {{-- Toggle sidebar --}}
  <button type="button" class="icon-btn" id="btnSidebarToggle" aria-label="Toggle sidebar">
    <span class="burger" aria-hidden="true"></span>
  </button>

  {{-- Search --}}
  <div class="search">
    <div class="searchbox">
      <img src="{{ asset('images/searching-icon.png') }}" alt="" class="icon">
      <input type="text" placeholder="Search for project" aria-label="Search projects">
    </div>
  </div>

  {{-- Right actions --}}
  <div class="top-actions" id="topActions">
    <button type="button"
            class="icon-btn"
            id="btnSettings"
            aria-label="Settings"
            aria-haspopup="menu"
            aria-expanded="false"
            aria-controls="menuSettings">
      <img src="{{ asset('images/setting-icon.png') }}" alt="Settings" class="icon">
    </button>

    {{-- Dropdown --}}
    <div id="menuSettings" role="menu" class="dropdown hidden" aria-labelledby="btnSettings">
      <a href="{{ route('profile.edit') }}" role="menuitem" class="dropdown-item">
        <img class="icon" src="{{ asset('images/profile-icon.png') }}" alt="">
        <span>Profil</span>
      </a>

      <a href="{{ route('notifications.index') }}" role="menuitem" class="dropdown-item">
        <img class="icon" src="{{ asset('images/notification-icon.png') }}" alt="">
        <span>Notifikasi</span>
      </a>

      <div class="dropdown-sep" role="separator"></div>

      {{-- Dark mode toggle --}}
      <button type="button" role="menuitem" class="dropdown-item" id="btnDarkMode">
        <img class="icon" src="{{ asset('images/moon-icon.png') }}" alt="">
        <span>Mode Gelap</span>
        <span class="switch" aria-hidden="true">
          <span class="knob"></span>
        </span>
      </button>

      <div class="dropdown-sep" role="separator"></div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" role="menuitem" class="dropdown-item">
          <img class="icon" src="{{ asset('images/logout-icon.png') }}" alt="">
          <span>Logout</span>
        </button>
      </form>
    </div>
  </div>
</header>
