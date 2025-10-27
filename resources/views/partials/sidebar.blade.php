<aside class="sidebar" id="sidebar">
    {{-- BRAND LOGO --}}
    <div class="brand">
        <img src="{{ asset('images/logo-wht-icon.png') }}" alt="Tracking" class="brand-logo">
    </div>
    <hr class="divider">

    {{-- NAVIGATION --}}
    <nav class="menu">
        <div class="menu-section">NAVIGATION</div>

        <a href="{{ route('dashboard') }}"
           class="menu-item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
            <img src="{{ asset('images/dashboard-icon.png') }}" alt="Dashboard" class="icon">
            <span>Dashboard</span>
        </a>

        <div class="menu-section">UI COMPONENT</div>

        <a href="{{ route('projects.index') }}"
           class="menu-item {{ request()->routeIs('projects.*') ? 'is-active' : '' }}">
            <img src="{{ asset('images/project-icon.png') }}" alt="Project" class="icon">
            <span>Project</span>
        </a>

        <a href="#"
           class="menu-item">
            <img src="{{ asset('images/contact-icon.png') }}" alt="Contact" class="icon">
            <span>Contact</span>
        </a>

        {{-- NEW: Report --}}
        <a href="{{ route('reports.index') }}"
           class="menu-item {{ request()->routeIs('reports.*') ? 'is-active' : '' }}">
            <img src="{{ asset('images/report-icon.png') }}" alt="Report" class="icon">
            <span>Report</span>
        </a>
    </nav>
</aside>
