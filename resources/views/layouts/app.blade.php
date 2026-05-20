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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
        .sidebar { position: fixed; left: 0; top: 0; height: 100vh; width: var(--sidebar-width); background: #ffffff; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1000; box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05); border-right: 1px solid var(--border-color); }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar-header { padding: 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); height: var(--topbar-height); background: #ffffff; }
        .sidebar-logo { display: flex; align-items: center; gap: 12px; color: var(--primary-green); text-decoration: none; font-weight: 700; font-size: 20px; transition: all 0.3s; }
        .sidebar-logo i { font-size: 28px; color: var(--primary-green); }
        .sidebar.collapsed .sidebar-logo-text { display: none; }
        .sidebar-toggle { background: var(--hover-green); border: 1px solid var(--light-green); color: var(--primary-green); width: 36px; height: 36px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
        .sidebar-toggle:hover { background: var(--light-green); transform: scale(1.05); }
        .sidebar-nav { padding: 20px 0; overflow-y: auto; height: calc(100vh - var(--topbar-height) - 80px); }
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-track { background: #f1f5f9; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .nav-item { margin: 4px 12px; }
        .nav-link { display: flex; align-items: center; padding: 12px 16px; color: var(--text-gray); text-decoration: none; border-radius: 10px; transition: all 0.2s; gap: 12px; font-weight: 500; font-size: 14px; position: relative; }
        .nav-link:hover { background: var(--hover-green); color: var(--primary-green); }
        .nav-link.active { background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .nav-link i { font-size: 18px; min-width: 20px; }
        .sidebar.collapsed .nav-link span { display: none; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 12px; }
        .submenu-indicator { margin-left: auto; font-size: 12px; transition: transform 0.3s ease; flex-shrink: 0; }
        .submenu-indicator.rotated { transform: rotate(180deg); }
        .sidebar.collapsed .submenu-indicator { display: none; }
        .sidebar-submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background-color: #f8fafc; border-radius: 8px; margin: 4px 0; }
        .sidebar-submenu.show { max-height: 600px; }
        .submenu-item { border-bottom: 1px solid #e2e8f0; }
        .submenu-item:last-child { border-bottom: none; }
        .submenu-item .nav-link { padding-left: 48px; font-size: 13px; font-weight: 500; }
        .submenu-item .nav-link:hover { background-color: var(--hover-green); color: var(--primary-green); }
        .submenu-item .nav-link.active { background: var(--light-green); color: var(--primary-green); box-shadow: none; }
        .submenu-item.has-nested-submenu .nav-link { position: relative; }
        .sidebar-nested-submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background-color: #f1f5f9; border-radius: 6px; margin: 4px 0 4px 12px; }
        .sidebar-nested-submenu.show { max-height: 400px; }
        .nested-submenu-item { border-bottom: 1px solid #e2e8f0; }
        .nested-submenu-item:last-child { border-bottom: none; }
        .nested-submenu-item .nav-link { padding-left: 64px; font-size: 12px; }
        .nested-submenu-item .nav-link:hover { background-color: #e0f2fe; color: var(--primary-green); }
        .nested-submenu-item .nav-link.active { background: #bae6fd; color: var(--primary-green); }
        .sidebar.collapsed .sidebar-submenu, .sidebar.collapsed .sidebar-nested-submenu { display: none; }
        .sidebar-footer { position: absolute; bottom: 0; width: 100%; padding: 16px; border-top: 1px solid var(--border-color); background: #ffffff; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 10px; transition: all 0.3s; cursor: pointer; }
        .user-profile:hover { background: var(--hover-green); }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; flex-shrink: 0; }
        .sidebar.collapsed .user-info { display: none; }
        .user-name { font-weight: 600; font-size: 14px; color: var(--text-dark); }
        .user-role { font-size: 12px; color: var(--text-gray); }
        .main-wrapper { margin-left: var(--sidebar-width); transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); min-height: 100vh; }
        .main-wrapper.expanded { margin-left: var(--sidebar-collapsed-width); }
        .topbar { background: white; height: var(--topbar-height); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); position: sticky; top: 0; z-index: 999; border-bottom: 1px solid var(--border-color); }
        .topbar-left { display: flex; align-items: center; gap: 20px; }
        .topbar-left h1 { font-size: 24px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .breadcrumb-custom { background: none; padding: 0; margin: 0; font-size: 13px; }
        .breadcrumb-custom .breadcrumb-item { color: var(--text-gray); }
        .breadcrumb-custom .breadcrumb-item.active { color: var(--primary-green); }
        .breadcrumb-custom a { color: var(--text-gray); text-decoration: none; }
        .breadcrumb-custom a:hover { color: var(--primary-green); }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-icon { width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; position: relative; border: 1px solid var(--border-color); }
        .topbar-icon:hover { background: var(--hover-green); color: var(--primary-green); border-color: var(--light-green); }
        .topbar-icon .badge { position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; font-weight: 600; }
        .dropdown-toggle::after { display: none; }
        .dropdown-menu { border: 1px solid var(--border-color); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); border-radius: 12px; margin-top: 8px; padding: 8px; }
        .dropdown-item { border-radius: 8px; padding: 10px 12px; transition: all 0.2s; font-size: 14px; }
        .dropdown-item:hover { background: var(--hover-green); color: var(--primary-green); }
        .content { padding: 30px; }
        .mobile-menu-btn { display: none; width: 40px; height: 40px; border-radius: 10px; background: var(--hover-green); color: var(--primary-green); border: 1px solid var(--light-green); align-items: center; justify-content: center; cursor: pointer; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999; opacity: 0; transition: opacity 0.3s; }
        .sidebar-overlay.show { display: block; opacity: 1; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .mobile-menu-btn { display: flex !important; }
            .topbar-left h1 { font-size: 18px; }
            .content { padding: 20px; }
        }
        .card { border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
        .card-header { background: white; border-bottom: 1px solid var(--border-color); padding: 20px; font-weight: 600; color: var(--text-dark); }
        .stats-card { background: white; border-radius: 12px; padding: 24px; border: 1px solid var(--border-color); transition: all 0.3s; }
        .stats-card:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transform: translateY(-2px); }
        .stats-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .stats-icon.green { background: var(--light-green); color: var(--primary-green); }
        .btn-success { background: var(--primary-green); border-color: var(--primary-green); }
        .btn-success:hover { background: var(--dark-green); border-color: var(--dark-green); }
        .text-gray-800 { color: var(--text-dark) !important; }
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
            <div class="nav-item" data-tooltip="Dashboard">
                <a href="" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-item has-submenu" data-tooltip="Data Master">
                <a class="nav-link menu-dropdown" href="#" data-menu="dataMaster">
                    <i class="fas fa-cogs"></i>
                    <span>Data Master</span>
                    <i class="fas fa-chevron-down submenu-indicator"></i>
                </a>
                <div class="sidebar-submenu {{ request()->is('master/*') ? 'show' : '' }}" id="dataMaster">
                    <div class="submenu-item">
                        <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                            <i class="fas fa-user-circle"></i>
                            <span>Customer</span>
                        </a>
                    </div>
                    <div class="submenu-item">
                        <a class="nav-link {{ request()->routeIs('contract.*') ? 'active' : '' }}" href="{{ route('contract.index') }}">
                            <i class="fas fa-building"></i>
                            <span>Contract</span>
                        </a>
                    </div>
                    <div class="submenu-item">
                        <a class="nav-link {{ request()->routeIs('area.*') ? 'active' : '' }}" href="{{ route('area.index') }}">
                            <i class="fas fa-globe"></i>
                            <span>Area</span>
                        </a>
                    </div>
                    <div class="submenu-item">
                        <a class="nav-link {{ request()->routeIs('invoice.*') ? 'active' : '' }}" href="{{ route('invoice.index') }}">
                            <i class="fas fa-project-diagram"></i>
                            <span>Invoice</span>
                        </a>
                    </div>
                    <div class="submenu-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-handshake"></i>
                            <span>Vessel</span>
                        </a>
                    </div>
                    <div class="submenu-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-anchor"></i>
                            <span>Port</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Data Dropdown -->
            <div class="nav-item has-submenu" data-tooltip="Manajemen Data">
                <a class="nav-link menu-dropdown" href="#" data-menu="manajemenData">
                    <i class="fas fa-briefcase"></i>
                    <span>Manajemen Data</span>
                    <i class="fas fa-chevron-down submenu-indicator"></i>
                </a>
                <div class="sidebar-submenu" id="manajemenData">
                    <!-- Data Kontrak with Nested Submenu -->
                    <div class="submenu-item has-nested-submenu">
                        <a class="nav-link menu-dropdown" href="#" data-menu="dataKontrakSub">
                            <i class="fas fa-scroll"></i>
                            <span>Job Order</span>
                            <i class="fas fa-chevron-down submenu-indicator"></i>
                        </a>
                        <div class="sidebar-nested-submenu" id="dataKontrakSub">
                            <div class="nested-submenu-item">
                                <a class="nav-link" href="{{route('jo-contract.index')}}">
                                    <i class="fas fa-pen-fancy"></i>
                                    <span>Contract</span>
                                </a>
                            </div>
                            <div class="nested-submenu-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-ship"></i>
                                    <span>Tramper</span>
                                </a>
                            </div>
                            <div class="nested-submenu-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-ellipsis-h"></i>
                                    <span>Other</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports -->
            <div class="nav-item" data-tooltip="Reports">
                <a href="#" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <span>Reports</span>
                </a>
            </div>

            <!-- Analytics -->
            <div class="nav-item" data-tooltip="Analytics">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-pie"></i>
                    <span>Analytics</span>
                </a>
            </div>

            <!-- Settings -->
            <div class="nav-item" data-tooltip="Pengaturan">
                <a href="#" class="nav-link">
                    <i class="fas fa-sliders-h"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
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
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            }
            setupEventListeners() {
                window.addEventListener('resize', () => this.checkScreenSize());
            }
            initDropdowns() {
                document.querySelectorAll('.menu-dropdown').forEach(toggle => {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        if (this.isCollapsed && !this.isMobile) return;
                        const menuId = toggle.getAttribute('data-menu');
                        const submenu = document.getElementById(menuId);
                        const arrow = toggle.querySelector('.submenu-indicator');
                        if (submenu && arrow) {
                            document.querySelectorAll('.sidebar-submenu.show').forEach(menu => {
                                if (menu !== submenu) {
                                    menu.classList.remove('show');
                                    const otherArrow = document.querySelector(`[data-menu="${menu.id}"] .submenu-indicator`);
                                    if (otherArrow) otherArrow.classList.remove('rotated');
                                }
                            });
                            submenu.classList.toggle('show');
                            arrow.classList.toggle('rotated');
                        }
                    });
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
                    document.querySelectorAll('.sidebar-submenu.show').forEach(menu => {
                        menu.classList.remove('show');
                        const arrow = document.querySelector(`[data-menu="${menu.id}"] .submenu-indicator`);
                        if (arrow) arrow.classList.remove('rotated');
                    });
                } else {
                    this.sidebar.classList.remove('collapsed');
                    this.mainWrapper.classList.remove('expanded');
                }
                this.saveState();
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
    </script>
    @stack('scripts')
</body>
</html>