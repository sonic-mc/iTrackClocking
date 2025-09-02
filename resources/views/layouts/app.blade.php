<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - iTrack Clocking</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⏰</text></svg>">
    
    <style>
        /* Reset and Base Styles */
        * { 
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-bg: #1e293b;
            --sidebar-width: 280px;
            --header-height: 70px;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        /* Header Navigation */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--secondary-color);
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover {
            background: #f1f5f9;
            color: var(--primary-color);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Header Right */
        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .clock-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .clock-status.clocked-in {
            background: #dcfce7;
            color: #166534;
        }

        .clock-status.clocked-out {
            background: #fef2f2;
            color: #991b1b;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .status-dot.active {
            background: var(--success-color);
        }

        .status-dot.inactive {
            background: var(--danger-color);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .user-menu {
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }

       /* Sidebar */
        .sidebar {
            --transition-timing: cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background-color: var(--sidebar-bg, white);
            border-right: 1px solid var(--border-color, #e2e8f0);
            overflow-y: auto;
            transition: transform 0.3s var(--transition-timing),
                        width 0.3s var(--transition-timing);
            z-index: 999;
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb, #cbd5e1) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: var(--scrollbar-thumb, #cbd5e1);
            border-radius: 3px;
        }

        .sidebar-content {
            padding: 1.5rem 0;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-color, #475569);
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            border-right: 3px solid transparent;
            position: relative;
            cursor: pointer;
        }

        .nav-item:hover {
            background-color: var(--hover-bg, #f8fafc);
            color: var(--primary-color);
            border-right-color: var(--primary-color);
        }

        .nav-item.active {
            background-color: var(--active-bg, #eff6ff);
            color: var(--primary-color);
            border-right-color: var(--primary-color);
            font-weight: 600;
        }

        .nav-icon {
            font-size: 1.25rem;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            .sidebar,
            .nav-item {
                transition: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }

        /* Quick Actions */
        .quick-actions {
            padding: 0 24px;
            margin-bottom: 24px;
        }

        .quick-clock-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .clock-in-btn {
            background: var(--success-color);
            color: white;
        }

        .clock-in-btn:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .clock-out-btn {
            background: var(--danger-color);
            color: white;
        }

        .clock-out-btn:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 24px;
            min-height: calc(100vh - var(--header-height));
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Page Header */
        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--secondary-color);
            font-size: 16px;
        }

        /* Breadcrumbs */
        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .breadcrumb-item {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .breadcrumb-item:hover {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: #1e293b;
            font-weight: 500;
        }

        .breadcrumb-separator {
            color: #cbd5e1;
        }

        /* Cards and Components */
        .card {
            background: white;
            border-radius: var(--border-radius);
            border: 1px solid #e2e8f0;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
        }

        .card-body {
            padding: 24px;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .badge.success {
            background: #dcfce7;
            color: #166534;
        }

        .badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge.danger {
            background: #fecaca;
            color: #991b1b;
        }

        .badge.info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 16px;
            }

            .main-content {
                padding: 16px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .header-right {
                gap: 12px;
            }

            .clock-status {
                display: none;
            }
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 90px;
            right: 24px;
            padding: 16px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            z-index: 1001;
            animation: slideInRight 0.3s ease-out;
            max-width: 400px;
        }

        .notification.success {
            background: white;
            border-left: 4px solid var(--success-color);
        }

        .notification.error {
            background: white;
            border-left: 4px solid var(--danger-color);
        }

        .notification.warning {
            background: white;
            border-left: 4px solid var(--warning-color);
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(100%);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f4f6;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Location status indicator */
        .location-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--secondary-color);
            background: #f8fafc;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .location-status.in-zone {
            background: #dcfce7;
            color: #166534;
        }

        .location-status.out-zone {
            background: #fecaca;
            color: #991b1b;
        }

        /* Utilities */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-4 { gap: 16px; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .text-primary { color: var(--primary-color); }
        .text-secondary { color: var(--secondary-color); }
        .hidden { display: none; }
        .block { display: block; }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <span id="menu-icon">☰</span>
            </button>
            <a href="{{ route('home') }}" class="logo">
                <div class="logo-icon">⏰</div>
                <span>iTrack Clocking</span>
            </a>
        </div>

        <div class="header-right">
            <!-- Clock Status -->
            {{-- <div class="clock-status {{ auth()->user()->isClockedIn() ? 'clocked-in' : 'clocked-out' }}" id="clockStatus">
                <div class="status-dot {{ auth()->user()->isClockedIn() ? 'active' : 'inactive' }}"></div>
                <span>{{ auth()->user()->isClockedIn() ? 'Clocked In' : 'Clocked Out' }}</span>
            </div> --}}

           <!-- Location Status -->
        <div class="location-status" id="locationStatus">
            <span>📍</span>
            <span id="locationText">Checking location...</span>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const locationText = document.getElementById("locationText");

            if ("geolocation" in navigator) {
                // Watch position for realtime updates
                navigator.geolocation.watchPosition(
                    (position) => {
                        const lat = position.coords.latitude.toFixed(6);
                        const lng = position.coords.longitude.toFixed(6);

                        locationText.textContent = `Lat: ${lat}, Lng: ${lng}`;
                    },
                    (error) => {
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                locationText.textContent = "Permission denied";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                locationText.textContent = "Location unavailable";
                                break;
                            case error.TIMEOUT:
                                locationText.textContent = "Location request timed out";
                                break;
                            default:
                                locationText.textContent = "Unknown error";
                                break;
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 0
                    }
                );
            } else {
                locationText.textContent = "Geolocation not supported";
            }
        });
        </script>


            <!-- Current Time -->
            <div class="text-sm text-secondary" id="currentTime"></div>

           <!-- User Menu -->
                <div class="user-menu">
                    @auth
                        <div class="user-avatar" onclick="toggleUserMenu()">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="user-name">
                            {{ auth()->user()->name }}
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
                    @endauth
                </div>

                
                
                <!-- User Dropdown (only show when logged in) -->
                @auth
                <div class="user-dropdown hidden" id="userDropdown">
                    <div class="user-info">
                        <div class="font-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-sm text-secondary">{{ auth()->user()->email }}</div>
                        <div class="text-xs text-secondary">{{ auth()->user()->role ?? 'Employee' }}</div>
                    </div>
                    <hr>
                    <a href="{{ route('profile') }}">Profile Settings</a>
                    <a href="{{ route('attendance.history') }}">My Attendance</a>
                
                    @if(auth()->user()->isManager())
                        <a href="{{ route('reports') }}">Reports</a>
                    @endif
                
                    <hr>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </div>
                @endauth
    </header>

    <!-- Sidebar -->
    @auth
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <!-- Quick Clock Actions -->
            <div class="quick-actions">
                <button class="quick-clock-btn clock-in-btn" onclick="quickClockIn()" style="{{ auth()->user()->isClockedIn() ? 'display:none' : '' }}">
                    <span>🕐</span>
                    Clock In
                </button>
                <button class="quick-clock-btn clock-out-btn" onclick="quickClockOut()" style="{{ !auth()->user()->isClockedIn() ? 'display:none' : '' }}">
                    <span>🕐</span>
                    Clock Out
                </button>
            </div>
            @endauth


            <!-- Navigation Menu -->
            <nav>
                <!-- Main Menu -->
                <div class="nav-section">
                    <div class="nav-title">Main</div>
                    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                    <form action="{{ route('employee.clock') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="nav-item {{ request()->routeIs('employee.clock') ? 'active' : '' }}" 
                                style="background:none; border:none; cursor:pointer; width:100%; text-align:left;">
                            <span class="nav-icon">⏰</span>
                            Clock In/Out
                        </button>
                    </form>
                    <a href="{{ route('attendance.history') }}" class="nav-item {{ request()->routeIs('attendance.history') ? 'active' : '' }}">
                        <span class="nav-icon">📅</span>
                        My Attendance
                    </a>
                </div>

                <!-- Time Management -->
                <div class="nav-section">
                    <div class="nav-title">Time Management</div>
                    <a href="{{ route('shifts.index') }}" class="nav-item {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
                        <span class="nav-icon">🔄</span>
                        Shift Schedule
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('breaks.*') ? 'active' : '' }}">
                        <span class="nav-icon">☕</span>
                        Breaks
                    </a>
                    <a href="{{ route('overtime.index') }}" class="nav-item {{ request()->routeIs('overtime.*') ? 'active' : '' }}">
                        <span class="nav-icon">⏳</span>
                        Overtime
                    </a>
                </div>

                <!-- Leave Management -->
                <div class="nav-section">
                    <div class="nav-title">Leave</div>
                    <a href="{{ route('leave.request') }}" class="nav-item {{ request()->routeIs('leaves.request') ? 'active' : '' }}">
                        <span class="nav-icon">📝</span>
                        Request Leave
                    </a>
                    <a href="{{ route('leave.history') }}" class="nav-item {{ request()->routeIs('leaves.history') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        Leave History
                    </a>
                </div>
                 
                @auth
                @if(auth()->user()->isManager() || auth()->user()->isManager())
                <!-- Management -->
                <div class="nav-section">
                    <div class="nav-title">Management</div>
                    <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        Employees
                    </a>
                    <a href="{{ route('employees.attendance') }}" class="nav-item {{ request()->routeIs('attendance.manage') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        Attendance Overview
                    </a>
                    <a href="{{ route('leave.approve') }}" class="nav-item {{ request()->routeIs('leaves.approve') ? 'active' : '' }}">
                        <span class="nav-icon">✅</span>
                        Approve Leaves
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('geofence.*') ? 'active' : '' }}">
                        <span class="nav-icon">🗺️</span>
                        Geofencing
                    </a>
                </div>

                <!-- Reports -->
                <div class="nav-section">
                    <div class="nav-title">Reports</div>
                    <a href="#" class="nav-item {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">
                        <span class="nav-icon">📈</span>
                        Attendance Reports
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('reports.payroll') ? 'active' : '' }}">
                        <span class="nav-icon">💰</span>
                        Payroll Reports
                    </a>
                    <a href="#" class="nav-item {{ request()->routeIs('reports.analytics') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        Analytics
                    </a>
                </div>
                @endif
                @endauth

                @auth
                @if(auth()->user()->isAdmin())
                <!-- System -->
                <div class="nav-section">
                    <div class="nav-title">System</div>
                    <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <span class="nav-icon">⚙️</span>
                        Settings
                    </a>
                    <a href="{{ route('admin.biometric') }}" class="nav-item {{ request()->routeIs('admin.biometric') ? 'active' : '' }}">
                        <span class="nav-icon">🔐</span>
                        Biometric Setup
                    </a>
                    <a href="{{ route('admin.audit') }}" class="nav-item {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
                        <span class="nav-icon">🔍</span>
                        Audit Logs
                    </a>
                </div>
                @endif
                @endauth
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs))
        <nav class="breadcrumbs">
            @foreach($breadcrumbs as $breadcrumb)
                @if($loop->last)
                    <span class="breadcrumb-item active">{{ $breadcrumb['title'] }}</span>
                @else
                    <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-item">{{ $breadcrumb['title'] }}</a>
                    <span class="breadcrumb-separator">›</span>
                @endif
            @endforeach
        </nav>
        @endif

        <!-- Page Header -->
        @hasSection('page-header')
        <div class="page-header">
            @yield('page-header')
        </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- User Dropdown Styles -->
    <style>
        .user-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            min-width: 220px;
            overflow: hidden;
            animation: fadeInDown 0.2s ease-out;
        }

        .user-dropdown .user-info {
            padding: 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .user-dropdown a {
            display: block;
            padding: 12px 16px;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .user-dropdown a:hover {
            background: #f8fafc;
            color: var(--primary-color);
        }

        .user-dropdown hr {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 0;
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <!-- Scripts -->
    <script>
        let sidebarOpen = window.innerWidth > 1024;
        let userMenuOpen = false;

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const menuIcon = document.getElementById('menu-icon');
            
            sidebarOpen = !sidebarOpen;
            sidebar.classList.toggle('show', sidebarOpen);
            menuIcon.textContent = sidebarOpen ? '✕' : '☰';
        }

        // Toggle User Menu
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            userMenuOpen = !userMenuOpen;
            dropdown.classList.toggle('hidden', !userMenuOpen);
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(e) {
            const userMenu = document.querySelector('.user-menu');
            if (!userMenu.contains(e.target) && userMenuOpen) {
                toggleUserMenu();
            }
        });

        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour12: true, 
                hour: 'numeric', 
                minute: '2-digit' 
            });
            document.getElementById('currentTime').textContent = timeString;
        }

        // Geolocation tracking
        function checkGeolocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        // Send to server to check if in geofence
                        fetch('/api/check-geofence', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ latitude: lat, longitude: lon })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const locationStatus = document.getElementById('locationStatus');
                            const locationText = document.getElementById('locationText');
                            
                            if (data.inGeofence) {
                                locationStatus.className = 'location-status in-zone';
                                locationText.textContent = 'In Work Zone';
                            } else {
                                locationStatus.className = 'location-status out-zone';
                                locationText.textContent = 'Outside Work Zone';
                            }
                        })
                        .catch(() => {
                            document.getElementById('locationText').textContent = 'Location unavailable';
                        });
                    },
                    function() {
                        document.getElementById('locationText').textContent = 'Location disabled';
                    }
                );
            }
        }

        // Quick Clock Actions
        function quickClockIn() {
            if (confirm('Are you sure you want to clock in?')) {
                const btn = event.target;
                btn.innerHTML = '<span class="loading-spinner"></span> Clocking In...';
                btn.disabled = true;
                
                fetch('/api/clock-in', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Successfully clocked in!', 'success');
                        updateClockStatus(true);
                    } else {
                        showNotification(data.message || 'Failed to clock in', 'error');
                    }
                })
                .catch(() => {
                    showNotification('Network error. Please try again.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = '<span>🕐</span> Clock In';
                    btn.disabled = false;
                });
            }
        }

        function quickClockOut() {
            if (confirm('Are you sure you want to clock out?')) {
                const btn = event.target;
                btn.innerHTML = '<span class="loading-spinner"></span> Clocking Out...';
                btn.disabled = true;

                fetch('/api/clock-out', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Successfully clocked out!', 'success');
                        updateClockStatus(false);
                    } else {
                        showNotification(data.message || 'Failed to clock out', 'error');
                    }
                })
                .catch(() => {
                    showNotification('Network error. Please try again.', 'error');
                })
                .finally(() => {
                    btn.innerHTML = '<span>🕐</span> Clock Out';
                    btn.disabled = false;
                });
            }
        }

        // Update UI clock status
        function updateClockStatus(isClockedIn) {
            const clockStatus = document.getElementById('clockStatus');
            const statusDot = clockStatus.querySelector('.status-dot');

            if (isClockedIn) {
                clockStatus.className = 'clock-status clocked-in';
                statusDot.className = 'status-dot active';
                clockStatus.querySelector('span').textContent = 'Clocked In';
                document.querySelector('.clock-in-btn').style.display = 'none';
                document.querySelector('.clock-out-btn').style.display = 'block';
            } else {
                clockStatus.className = 'clock-status clocked-out';
                statusDot.className = 'status-dot inactive';
                clockStatus.querySelector('span').textContent = 'Clocked Out';
                document.querySelector('.clock-in-btn').style.display = 'block';
                document.querySelector('.clock-out-btn').style.display = 'none';
            }
        }

        // Notification System
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Init on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateTime();
            setInterval(updateTime, 60000); // update every minute
            checkGeolocation();
        });
    </script>
</body>
</html>
