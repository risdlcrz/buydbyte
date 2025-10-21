<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'BuyDByte') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #1e40af;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f8fafc;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #334155;
        }

        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            margin-left: auto;
        }

        .nav-section {
            padding: 1rem 0;
        }

        .nav-section-title {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar.collapsed .nav-section-title {
            display: none;
        }
        
        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 0;
            margin: 0;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            border: none;
            display: flex;
            align-items: center;
            text-decoration: none;
            position: relative;
        }

        .sidebar.collapsed .nav-link {
            padding: 0.75rem;
            justify-content: center;
        }
        
        .sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            flex-shrink: 0;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .nav-link .nav-text {
            display: none;
        }

        .main-wrapper {
            margin-left: 280px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-wrapper.expanded {
            margin-left: 70px;
        }

        .main-content {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            margin-bottom: 0;
        }

        .page-header {
            background: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .page-title {
            color: #1e293b;
            font-weight: 600;
            margin: 0;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0.5rem 0 0 0;
        }

        .breadcrumb-item {
            color: var(--text-muted);
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
        }
        
        .stats-card.users {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }
        
        .stats-card.products {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .stats-card.categories {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stats-card.orders {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .quick-action-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.2s ease;
        }

        .quick-action-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        /* User dropdown button styling */
        .dropdown-toggle.btn-dark {
            background-color: var(--sidebar-bg) !important;
            border: 1px solid var(--sidebar-hover) !important;
            transition: all 0.2s ease;
        }

        .dropdown-toggle.btn-dark:hover {
            background-color: var(--sidebar-hover) !important;
            border-color: #475569 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .dropdown-toggle.btn-dark:focus {
            background-color: var(--sidebar-hover) !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        
        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .table thead th {
            background: #f8fafc;
            color: #1e293b;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border-radius: 12px;
            padding: 0;
            margin-top: 8px;
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .dropdown-menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            color: #374151 !important;
            text-decoration: none;
            background-color: transparent !important;
        }

        .dropdown-item:hover {
            background-color: #f8fafc !important;
            padding-left: 1.25rem;
            transform: translateX(4px);
            color: #1f2937 !important;
        }

        .dropdown-item:active,
        .dropdown-item:focus {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        .dropdown-item i {
            color: inherit;
        }

        .dropdown-item.text-danger {
            color: #dc2626 !important;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fef2f2 !important;
            color: #b91c1c !important;
        }

        .dropdown-header {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6b7280 !important;
            font-weight: 600;
        }

        /* Ensure proper text visibility */
        .dropdown-menu .text-dark {
            color: #374151 !important;
        }

        .dropdown-menu .text-muted {
            color: #6b7280 !important;
        }

        .dropdown-menu span {
            color: inherit;
        }

        /* Dropdown container styling */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        /* Ensure dropdown is always visible when shown */
        .dropdown-menu[data-bs-popper] {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
        }

        .alert {
            border: none;
            border-radius: 8px;
            padding: 1rem 1.5rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .main-wrapper.expanded {
                margin-left: 0;
            }

            .mobile-menu-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }

            .mobile-menu-overlay.show {
                display: block;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center">
            <h4 class="text-white mb-0">
                <i class="bi bi-shop"></i>
                <span class="nav-text">BuyDByte</span>
            </h4>
            <button class="sidebar-toggle ms-auto d-none d-lg-block" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <nav class="nav flex-column">
            <!-- Dashboard Section -->
            <div class="nav-section">
                <div class="nav-section-title">Overview</div>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>

            <!-- E-commerce Management -->
            <div class="nav-section">
                <div class="nav-section-title">E-commerce</div>
                
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" 
                   href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam"></i>
                    <span class="nav-text">Products</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                   href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i>
                    <span class="nav-text">Categories</span>
                </a>

                <!-- Future: Orders, Inventory, etc. -->
                <!--
                <a class="nav-link" href="#">
                    <i class="bi bi-receipt"></i>
                    <span class="nav-text">Orders</span>
                </a>
                
                <a class="nav-link" href="#">
                    <i class="bi bi-graph-up"></i>
                    <span class="nav-text">Inventory</span>
                </a>
                -->
            </div>

            <!-- Product Configuration -->
            <div class="nav-section">
                <div class="nav-section-title">Configuration</div>
                
                <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" 
                   href="{{ route('admin.attributes.index') }}">
                    <i class="bi bi-sliders"></i>
                    <span class="nav-text">Attribute Definitions</span>
                </a>
                
                <a class="nav-link {{ request()->routeIs('admin.product-attributes.*') ? 'active' : '' }}" 
                   href="{{ route('admin.product-attributes.index') }}">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Product Attributes</span>
                </a>

                <a class="nav-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" 
                   href="{{ route('admin.promotions.index') }}">
                    <i class="bi bi-percent"></i>
                    <span class="nav-text">Promotions</span>
                </a>
            </div>

            <!-- User Management -->
            <div class="nav-section">
                <div class="nav-section-title">Users</div>
                
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                   href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">All Users</span>
                </a>

                <!-- Future: Customers, Admins, Roles -->
                <!--
                <a class="nav-link" href="#">
                    <i class="bi bi-person-badge"></i>
                    <span class="nav-text">Customers</span>
                </a>
                
                <a class="nav-link" href="#">
                    <i class="bi bi-shield-check"></i>
                    <span class="nav-text">Admins</span>
                </a>
                -->
            </div>

            <!-- Reports & Analytics -->
            <div class="nav-section">
                <div class="nav-section-title">Analytics</div>
                
                <!-- Future: Reports, Analytics -->
                <!--
                <a class="nav-link" href="#">
                    <i class="bi bi-bar-chart"></i>
                    <span class="nav-text">Sales Reports</span>
                </a>
                
                <a class="nav-link" href="#">
                    <i class="bi bi-pie-chart"></i>
                    <span class="nav-text">Analytics</span>
                </a>
                -->
                
                <a class="nav-link disabled" href="#">
                    <i class="bi bi-bar-chart"></i>
                    <span class="nav-text">Coming Soon</span>
                </a>
            </div>

            <!-- Settings & Tools -->
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                
                <!-- Future: Settings, Tools -->
                <!--
                <a class="nav-link" href="#">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Settings</span>
                </a>
                
                <a class="nav-link" href="#">
                    <i class="bi bi-tools"></i>
                    <span class="nav-text">Tools</span>
                </a>
                -->
                
                <a class="nav-link" href="{{ route('storefront.home') }}" target="_blank">
                    <i class="bi bi-arrow-up-right-square"></i>
                    <span class="nav-text">View Store</span>
                </a>
            </div>
        </nav>
    </div>
    <!-- Main Content Wrapper -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Top Navigation -->
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <!-- Mobile Menu Toggle -->
                <button class="btn btn-link d-lg-none me-3" id="mobileMenuToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                
                <div>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @else
                                <li class="breadcrumb-item active">@yield('page-title', 'Dashboard')</li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" style="border-radius: 12px; background-color: var(--sidebar-bg); border: 1px solid var(--sidebar-hover);">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <span class="text-white fw-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="text-start d-none d-md-block">
                                <div class="fw-medium text-white" style="font-size: 0.875rem;">{{ Str::limit(auth()->user()->name, 15) }}</div>
                                <div class="small text-white-50">{{ ucfirst(auth()->user()->role) }}</div>
                            </div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 280px;">
                        <!-- User Info Header -->
                        <li class="px-3 py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                     style="width: 48px; height: 48px;">
                                    <span class="text-white fw-bold h6 mb-0">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                    <div class="small text-muted">{{ auth()->user()->email }}</div>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ auth()->user()->role === 'admin' ? 'primary' : 'secondary' }} small">
                                            {{ ucfirst(auth()->user()->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        
                        <!-- Navigation Links -->
                        <li><a class="dropdown-item py-2 text-dark" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-3 text-primary"></i>
                            <span>Admin Dashboard</span>
                        </a></li>
                        <li><a class="dropdown-item py-2 text-dark" href="{{ route('admin.users.show', auth()->user()) }}">
                            <i class="bi bi-person me-3 text-info"></i>
                            <span>My Profile</span>
                        </a></li>
                        
                        <li><hr class="dropdown-divider my-1"></li>
                        
                        <!-- Quick Actions -->
                        <li class="dropdown-header small text-muted fw-bold">QUICK ACTIONS</li>
                        <li><a class="dropdown-item py-2 text-dark" href="{{ route('storefront.home') }}" target="_blank">
                            <i class="bi bi-shop me-3 text-warning"></i>
                            <span>View Store</span>
                            <i class="bi bi-arrow-up-right ms-auto small"></i>
                        </a></li>
                        <li><a class="dropdown-item py-2 text-dark" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people me-3 text-secondary"></i>
                            <span>Manage Users</span>
                        </a></li>
                        
                        <li><hr class="dropdown-divider my-1"></li>
                        
                        <!-- Account Actions -->
                        <li class="dropdown-header small text-muted fw-bold">ACCOUNT</li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-3"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content p-4">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin Dashboard Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            
            // Desktop sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainWrapper.classList.toggle('expanded');
                });
            }
            
            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.add('show');
                    mobileMenuOverlay.classList.add('show');
                });
            }
            
            // Close mobile menu
            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    mobileMenuOverlay.classList.remove('show');
                });
            }
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
    
    @stack('scripts')
</body>
</html>