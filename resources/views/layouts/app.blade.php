<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>

    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    <style>
        :root {
            --primary-green: #10b981;
            --light-green: #d1fae5;
            --dark-green: #059669;
            --hover-green: #ecfdf5;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --topbar-height: 70px;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --border-color: #e2e8f0;

        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.08);
            border-right: 1px solid var(--border-color);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            height: var(--topbar-height);
            background: #ffffff;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 700;
            font-size: 20px;
            transition: all 0.3s;
        }

        .sidebar-logo i {
            font-size: 28px;
            color: var(--primary-green);
        }

        .sidebar.collapsed .sidebar-logo-text {
            display: none;
        }

        .sidebar-toggle {
            background: var(--hover-green);
            border: 1px solid var(--light-green);
            color: var(--primary-green);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .sidebar-toggle:hover {
            background: var(--light-green);
            transform: scale(1.05);
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            padding: 20px 0;
            overflow-y: auto;
            height: calc(100vh - var(--topbar-height) - 80px);
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .nav-item {
            margin: 4px 12px;
            position: relative;
        }

        /* Tooltip for collapsed sidebar */
        .nav-item[data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--text-dark);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            z-index: 1001;
        }

        .sidebar.collapsed .nav-item[data-tooltip]:hover::after {
            opacity: 1;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-gray);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            gap: 12px;
            font-weight: 500;
            font-size: 14px;
            position: relative;
            cursor: pointer;
        }

        .nav-link:hover {
            background: var(--hover-green);
            color: var(--primary-green);
            transform: translateX(2px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .nav-link i {
            font-size: 18px;
            min-width: 20px;
            transition: transform 0.3s;
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        /* Submenu Indicator */
        .submenu-indicator {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .nav-link.menu-open .submenu-indicator {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .submenu-indicator {
            display: none;
        }

        /* Submenu Styles */
        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f8fafc;
            border-radius: 8px;
            margin: 4px 0;
        }

        .sidebar-submenu.show {
            max-height: 800px;
        }

        .submenu-item {
            border-bottom: 1px solid #e2e8f0;
        }

        .submenu-item:last-child {
            border-bottom: none;
        }

        .submenu-item .nav-link {
            padding-left: 48px;
            font-size: 13px;
            font-weight: 500;
        }

        .submenu-item .nav-link:hover {
            background-color: var(--hover-green);
            color: var(--primary-green);
        }

        .submenu-item .nav-link.active {
            background: var(--light-green);
            color: var(--primary-green);
            box-shadow: none;
            font-weight: 600;
        }

        /* Nested Submenu */
        .submenu-item.has-nested-submenu .nav-link {
            position: relative;
        }

        .sidebar-nested-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f1f5f9;
            border-radius: 6px;
            margin: 4px 0 4px 12px;
        }

        .sidebar-nested-submenu.show {
            max-height: 500px;
        }

        .nested-submenu-item {
            border-bottom: 1px solid #e2e8f0;
        }

        .nested-submenu-item:last-child {
            border-bottom: none;
        }

        .nested-submenu-item .nav-link {
            padding-left: 64px;
            font-size: 12px;
        }

        .nested-submenu-item .nav-link:hover {
            background-color: #e0f2fe;
            color: var(--primary-green);
        }

        .nested-submenu-item .nav-link.active {
            background: #bae6fd;
            color: var(--primary-green);
        }

        .sidebar.collapsed .sidebar-submenu,
        .sidebar.collapsed .sidebar-nested-submenu {
            display: none;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 16px;
            border-top: 1px solid var(--border-color);
            background: #ffffff;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .user-profile:hover {
            background: var(--hover-green);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        .sidebar.collapsed .user-info {
            display: none;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
        }

        .user-role {
            font-size: 12px;
            color: var(--text-gray);
        }

        /* Main Wrapper - FIXED */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }

        .main-wrapper.expanded {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* Topbar */
        .topbar {
            background: white;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--border-color);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .topbar-left h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .breadcrumb-custom {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }

        .breadcrumb-custom .breadcrumb-item {
            color: var(--text-gray);
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--primary-green);
        }

        .breadcrumb-custom a {
            color: var(--text-gray);
            text-decoration: none;
        }

        .breadcrumb-custom a:hover {
            color: var(--primary-green);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            border: 1px solid var(--border-color);
        }

        .topbar-icon:hover {
            background: var(--hover-green);
            color: var(--primary-green);
            border-color: var(--light-green);
        }

        .topbar-icon .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: 600;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            margin-top: 8px;
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.2s;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: var(--hover-green);
            color: var(--primary-green);
        }

        .content {
            padding: 30px;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--hover-green);
            color: var(--primary-green);
            border: 1px solid var(--light-green);
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .mobile-menu-btn {
                display: flex !important;
            }

            .topbar-left h1 {
                font-size: 18px;
            }

            .content {
                padding: 20px;
            }
        }

        /* Additional Styles */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }

        .stats-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stats-icon.green {
            background: var(--light-green);
            color: var(--primary-green);
        }

        .btn-success {
            background: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-success:hover {
            background: var(--dark-green);
            border-color: var(--dark-green);
        }

        .text-gray-800 {
            color: var(--text-dark) !important;
        }

        /* PERBAIKAN: Table dan Card responsive terhadap sidebar */
        .table-responsive {
            transition: all 0.3s ease;
        }

        /* DataTables wrapper juga perlu smooth transition */
        .dataTables_wrapper {
            transition: all 0.3s ease;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-color: var(--border-color);
            border-radius: 6px;
            font-size: 14px;
            min-height: 38px;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            padding: 6px 12px;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: var(--primary-green);
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--primary-green) !important;
        }

        .select2-container--bootstrap-5 .select2-search__field:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.15rem rgba(16, 185, 129, 0.2);
        }

        /* Sesuaikan lebar agar mengikuti parent */
        .select2-container {
            width: 100% !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="" class="sidebar-logo">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span class="sidebar-logo-text">Jenius</span>
            </a>
            <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            {{-- Dashboard - Semua user bisa akses --}}
            <div class="nav-item" data-tooltip="Dashboard">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            {{-- User Management --}}
            {{-- @if (auth()->check() && (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('user_management')))
                <div class="nav-item has-submenu" data-tooltip="Manajemen Pengguna">
                    <a class="nav-link menu-dropdown" href="javascript:void(0)" data-menu="manajemenUser">
                        <i class="fas fa-users"></i>
                        <span>User Management</span>
                        <i class="fas fa-chevron-down submenu-indicator"></i>
                    </a>
                    <div class="sidebar-submenu {{ request()->is('admin/user*') ? 'show' : '' }}" id="manajemenUser">
                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('user_management'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('user.*') && !request()->routeIs('user-access.*') ? 'active' : '' }}"
                                    href="{{ route('user.index') }}">
                                    <i class="fas fa-user"></i>
                                    <span>Users</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif --}}

            {{-- Data Master --}}
            @if (auth()->check() &&
                    (auth()->user()->is_admin ||
                        auth()->user()->hasAccessToMenu('customer') ||
                        auth()->user()->hasAccessToMenu('contract') ||
                        auth()->user()->hasAccessToMenu('area') ||
                        auth()->user()->hasAccessToMenu('invoice') ||
                        auth()->user()->hasAccessToMenu('vessel') ||
                        auth()->user()->hasAccessToMenu('port') ||
                        auth()->user()->hasAccessToMenu('other')))
                <div class="nav-item has-submenu" data-tooltip="Data Master">
                    <a class="nav-link menu-dropdown" href="javascript:void(0)" data-menu="dataMaster">
                        <i class="fas fa-cogs"></i>
                        <span>Data Master</span>
                        <i class="fas fa-chevron-down submenu-indicator"></i>
                    </a>
                    <div class="sidebar-submenu {{ request()->is('master/*') ? 'show' : '' }}" id="dataMaster">
                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('customer'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}"
                                    href="{{ route('customer.index') }}">
                                    <i class="fas fa-user-circle"></i>
                                    <span>Customer</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('contract'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('contract.*') ? 'active' : '' }}"
                                    href="{{ route('contract.index') }}">
                                    <i class="fas fa-building"></i>
                                    <span>Contract</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('area'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('area.*') ? 'active' : '' }}"
                                    href="{{ route('area.index') }}">
                                    <i class="fas fa-globe"></i>
                                    <span>Area</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('invoice'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('invoice.*') ? 'active' : '' }}"
                                    href="{{ route('invoice.index') }}">
                                    <i class="fas fa-project-diagram"></i>
                                    <span>Invoice</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('vessel'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('vessel.*') ? 'active' : '' }}"
                                    href="{{ route('vessel.index') }}">
                                    <i class="fas fa-ship"></i>
                                    <span>Vessel</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('port'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('port.*') ? 'active' : '' }}"
                                    href="{{ route('port.index') }}">
                                    <i class="fas fa-anchor"></i>
                                    <span>Port</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('other'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('other.*') ? 'active' : '' }}"
                                    href="{{ route('other.index') }}">
                                    <i class="fas fa-ellipsis-h"></i>
                                    <span>Other</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Job Order --}}
            @if (auth()->check() &&
                    (auth()->user()->is_admin ||
                        auth()->user()->hasAccessToMenu('jo_contract') ||
                        auth()->user()->hasAccessToMenu('jo_tramper') ||
                        auth()->user()->hasAccessToMenu('jo_other')))
                <div class="nav-item has-submenu" data-tooltip="Job Order">
                    <a class="nav-link menu-dropdown" href="javascript:void(0)" data-menu="manajemenData">
                        <i class="fas fa-briefcase"></i>
                        <span>Job Order</span>
                        <i class="fas fa-chevron-down submenu-indicator"></i>
                    </a>
                    <div class="sidebar-submenu {{ request()->is('jo-contract*') ? 'show' : '' }}" id="manajemenData">
                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('jo_contract'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('jo-contract.*') ? 'active' : '' }}"
                                    href="{{ route('jo-contract.index') }}">
                                    <i class="fas fa-file-contract"></i>
                                    <span>JO Contract</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('jo_tramper'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('jo-tramper.*') ? 'active' : '' }}"
                                    href="{{ route('jo-tramper.index') }}">
                                    <i class="fas fa-truck-moving"></i>
                                    <span>JO Tramper</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->user()->is_admin || auth()->user()->hasAccessToMenu('jo_other'))
                            <div class="submenu-item">
                                <a class="nav-link {{ request()->routeIs('jo-other.*') ? 'active' : '' }}"
                                    href="{{ route('jo-other.index') }}">
                                    <i class="fas fa-ellipsis-h"></i>
                                    <span>JO Other</span>
                                </a>
                            </div>
                        @endif


                    </div>
                </div>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">A</div>
                <div class="user-info">
                    <div class="user-name">Admin User</div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main-wrapper" id="mainWrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button class="mobile-menu-btn" id="mobileMenuBtn" onclick="openMobileSidebar()">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="topbar-right">
                <div class="dropdown">
                    <button class="topbar-icon dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a>
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item"
                                    style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- jQuery from CDN - MUST BE FIRST -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        class SidebarManager {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.mainWrapper = document.getElementById('mainWrapper');
                this.overlay = document.getElementById('sidebarOverlay');
                this.isCollapsed = false;
                this.isMobile = window.innerWidth <= 768;
                this.init();
            }

            init() {
                this.loadState();
                this.setupEventListeners();
                this.checkScreenSize();
                this.initDropdowns();
                this.initActiveStates();
            }

            setupEventListeners() {
                window.addEventListener('resize', () => this.checkScreenSize());
            }

            initDropdowns() {
                // Handle all dropdown menus
                document.querySelectorAll('.menu-dropdown').forEach(toggle => {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        // Don't open dropdowns in collapsed mode (desktop only)
                        if (this.isCollapsed && !this.isMobile) return;

                        const menuId = toggle.getAttribute('data-menu');
                        const submenu = document.getElementById(menuId);

                        if (submenu) {
                            // Check if this submenu is currently open
                            const isOpen = submenu.classList.contains('show');

                            // If it's a nested submenu, only close siblings at the same level
                            if (submenu.classList.contains('sidebar-nested-submenu')) {
                                // Close sibling nested submenus only
                                const parent = submenu.closest('.sidebar-submenu');
                                if (parent) {
                                    parent.querySelectorAll('.sidebar-nested-submenu.show').forEach(
                                        menu => {
                                            if (menu !== submenu) {
                                                menu.classList.remove('show');
                                                const otherToggle = document.querySelector(
                                                    `[data-menu="${menu.id}"]`);
                                                if (otherToggle) otherToggle.classList.remove(
                                                    'menu-open');
                                            }
                                        });
                                }
                            } else {
                                // Close other main level submenus
                                document.querySelectorAll('.sidebar-submenu.show').forEach(menu => {
                                    if (menu !== submenu) {
                                        menu.classList.remove('show');
                                        // Also close nested submenus inside
                                        menu.querySelectorAll('.sidebar-nested-submenu.show')
                                            .forEach(nested => {
                                                nested.classList.remove('show');
                                                const nestedToggle = document.querySelector(
                                                    `[data-menu="${nested.id}"]`);
                                                if (nestedToggle) nestedToggle.classList
                                                    .remove('menu-open');
                                            });
                                        const otherToggle = document.querySelector(
                                            `[data-menu="${menu.id}"]`);
                                        if (otherToggle) otherToggle.classList.remove(
                                            'menu-open');
                                    }
                                });
                            }

                            // Toggle the clicked submenu
                            if (isOpen) {
                                submenu.classList.remove('show');
                                toggle.classList.remove('menu-open');
                                // Close nested submenus if closing parent
                                submenu.querySelectorAll('.sidebar-nested-submenu.show').forEach(
                                    nested => {
                                        nested.classList.remove('show');
                                        const nestedToggle = document.querySelector(
                                            `[data-menu="${nested.id}"]`);
                                        if (nestedToggle) nestedToggle.classList.remove(
                                            'menu-open');
                                    });
                            } else {
                                submenu.classList.add('show');
                                toggle.classList.add('menu-open');
                            }
                        }
                    });
                });
            }

            initActiveStates() {
                // Auto-open submenus containing active links
                document.querySelectorAll('.nav-link.active').forEach(activeLink => {
                    let parentSubmenu = activeLink.closest('.sidebar-submenu');
                    while (parentSubmenu) {
                        parentSubmenu.classList.add('show');
                        const toggle = document.querySelector(`[data-menu="${parentSubmenu.id}"]`);
                        if (toggle) toggle.classList.add('menu-open');
                        parentSubmenu = parentSubmenu.parentElement.closest('.sidebar-submenu');
                    }

                    // Also check for nested submenus
                    let nestedSubmenu = activeLink.closest('.sidebar-nested-submenu');
                    while (nestedSubmenu) {
                        nestedSubmenu.classList.add('show');
                        const nestedToggle = document.querySelector(`[data-menu="${nestedSubmenu.id}"]`);
                        if (nestedToggle) nestedToggle.classList.add('menu-open');
                        nestedSubmenu = nestedSubmenu.parentElement.closest('.sidebar-nested-submenu');
                    }
                });
            }

            checkScreenSize() {
                const wasMobile = this.isMobile;
                this.isMobile = window.innerWidth <= 768;

                if (wasMobile !== this.isMobile) {
                    if (this.isMobile) {
                        this.sidebar.classList.remove('collapsed');
                        this.mainWrapper.classList.remove('expanded');
                        this.closeMobileSidebar();
                    } else {
                        this.sidebar.classList.remove('mobile-open');
                        this.overlay.classList.remove('show');
                        this.loadState();
                    }
                }
            }

            toggle() {
                if (this.isMobile) {
                    this.toggleMobile();
                } else {
                    this.toggleDesktop();
                }
            }

            toggleMobile() {
                const isOpen = this.sidebar.classList.contains('mobile-open');
                if (isOpen) {
                    this.closeMobileSidebar();
                } else {
                    this.openMobileSidebar();
                }
            }

            openMobileSidebar() {
                this.sidebar.classList.add('mobile-open');
                this.overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            closeMobileSidebar() {
                this.sidebar.classList.remove('mobile-open');
                this.overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            toggleDesktop() {
                this.isCollapsed = !this.isCollapsed;

                if (this.isCollapsed) {
                    this.sidebar.classList.add('collapsed');
                    this.mainWrapper.classList.add('expanded');
                    // Close all submenus when collapsing
                    document.querySelectorAll('.sidebar-submenu.show, .sidebar-nested-submenu.show').forEach(menu => {
                        menu.classList.remove('show');
                        const toggle = document.querySelector(`[data-menu="${menu.id}"]`);
                        if (toggle) toggle.classList.remove('menu-open');
                    });
                } else {
                    this.sidebar.classList.remove('collapsed');
                    this.mainWrapper.classList.remove('expanded');
                    // Restore active menu states
                    this.initActiveStates();
                }

                this.saveState();

                // Trigger DataTable column adjustment jika ada
                this.adjustDataTable();
            }

            adjustDataTable() {
                // Tunggu transisi selesai baru adjust DataTable
                setTimeout(() => {
                    if (typeof $.fn.DataTable !== 'undefined') {
                        $.fn.DataTable.tables({
                            visible: true,
                            api: true
                        }).columns.adjust();
                    }
                }, 350); // Sesuai dengan durasi transisi CSS (0.3s + buffer)
            }

            saveState() {
                if (!this.isMobile) {
                    localStorage.setItem('sidebarCollapsed', this.isCollapsed);
                }
            }

            loadState() {
                if (!this.isMobile) {
                    const savedState = localStorage.getItem('sidebarCollapsed');
                    if (savedState !== null) {
                        this.isCollapsed = savedState === 'true';
                        if (this.isCollapsed) {
                            this.sidebar.classList.add('collapsed');
                            this.mainWrapper.classList.add('expanded');
                        } else {
                            this.sidebar.classList.remove('collapsed');
                            this.mainWrapper.classList.remove('expanded');
                        }
                    }
                }
            }
        }

        let sidebarManager;

        document.addEventListener('DOMContentLoaded', () => {
            sidebarManager = new SidebarManager();
        });

        function toggleSidebar() {
            sidebarManager?.toggle();
        }

        function openMobileSidebar() {
            sidebarManager?.openMobileSidebar();
        }

        function closeMobileSidebar() {
            sidebarManager?.closeMobileSidebar();
        }

        // ===== SELECT2 GLOBAL INIT =====
        // Semua <select> dengan class "select2" akan otomatis diinisiasi
        $(document).ready(function() {
            initSelect2();
        });

        function initSelect2(context) {
            const scope = context || document;
            $(scope).find('select.select2').each(function() {
                const placeholder = $(this).data('placeholder') || 'Select an option';
                const allowClear = $(this).data('allow-clear') !== false; // default true

                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: placeholder,
                    allowClear: allowClear,
                    width: '100%',
                });
            });
        }
    </script>
    @stack('scripts')
</body>

</html>
