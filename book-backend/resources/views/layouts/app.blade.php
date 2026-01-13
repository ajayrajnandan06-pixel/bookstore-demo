<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookstore Admin')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --sidebar-width: 250px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            transition: all 0.3s;
            z-index: 1000;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        #sidebar .sidebar-header h3 {
            margin: 0;
            font-weight: 600;
        }
        #sidebar .sidebar-header h3 i {
            margin-right: 10px;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 15px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        #sidebar ul li a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid white;
        }
        #sidebar ul li a.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }
        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        #sidebar .user-info {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            position: absolute;
            bottom: 0;
            width: 100%;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        #sidebar .user-info .user-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 10px;
        }
        
        /* Main Content */
        #content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .top-navbar .navbar-brand {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        /* Cards */
        .dashboard-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .dashboard-card .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        /* Stats Cards Colors */
        .card-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
        .card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        .card-warning {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            color: white;
        }
        .card-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            color: white;
        }
        
        /* Table */
        .dataTable {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .dataTable thead th {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
        }
        
        /* Alerts */
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -var(--sidebar-width);
            }
            #content {
                margin-left: 0;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content.active {
                margin-left: var(--sidebar-width);
            }
            .sidebar-toggle {
                display: block !important;
            }
        }
        
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar Toggle Button (Mobile) -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-book"></i> Bookstore Admin</h3>
        </div>
        
        <ul class="list-unstyled components">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('books.index') }}" class="{{ request()->is('books*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Books
                </a>
            </li>
            <li>
    <a href="#customersSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
        <i class="fas fa-users"></i> Customers
    </a>
    <ul class="collapse list-unstyled" id="customersSubmenu">
        <li>
            <a href="{{ route('customers.index') }}">
                <i class="fas fa-list"></i> All Customers
            </a>
        </li>
        <li>
            <a href="{{ route('customers.create') }}">
                <i class="fas fa-user-plus"></i> Add Customer
            </a>
        </li>
    </ul>
</li>
            <li>
                <a href="#ordersSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                    <i class="fas fa-shopping-cart"></i> Orders
                </a>
                <ul class="collapse list-unstyled" id="ordersSubmenu">
                    <a href="{{ route('orders.index') }}"class="{{ request()->is('orders*') ? 'active' : '' }}"><i class="fas fa-list"></i> All Orders
</a>
<a href="{{ route('orders.create') }}"><i class="fas fa-plus"></i> Create New Order</a>
                </ul>
            </li>
            
            <li>
                <a href="#invoicesSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                    <i class="fas fa-file-invoice"></i> Invoices
                </a>
                <ul class="collapse list-unstyled" id="invoicesSubmenu">
                    <li><a href="#"><i class="fas fa-list"></i> All Invoices</a></li>
                    <li><a href="#"><i class="fas fa-plus-circle"></i> Create Invoice</a></li>
                </ul>
            </li>
            <li>
                <a href="#reportsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <ul class="collapse list-unstyled" id="reportsSubmenu">
                    <li><a href="#"><i class="fas fa-chart-line"></i> Sales Report</a></li>
                    <li><a href="#"><i class="fas fa-box"></i> Inventory Report</a></li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
        
        <div class="user-info d-flex align-items-center">
            <div class="user-img">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h6 class="mb-0">{{ Auth::user()->name ?? 'User' }}</h6>
                <small>{{ Auth::user()->email ?? 'email@example.com' }}</small>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                        <small class="text-muted">@yield('page-subtitle', 'Welcome to Bookstore Admin Panel')</small>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                        <button class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content Area -->
        <div class="container-fluid">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Page Content -->
            @yield('content')
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    // Custom JS
<script>
    $(document).ready(function() {
        // Sidebar Toggle for Mobile
        $('#sidebarToggle').click(function() {
            $('#sidebar').toggleClass('active');
            $('#content').toggleClass('active');
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
        
        // Set CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
    
    @yield('scripts')
</body>
</html>