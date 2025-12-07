<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $pageTitle ?? 'BudgetPlanner'; ?></title>
    
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom overrides -->
    <?php 
    $base_path = dirname($_SERVER['PHP_SELF']);
    $css_path = ($base_path === '/public' || strpos($base_path, '/public') !== false) ? '../assets/css/style.css' : 'assets/css/style.css';
    ?>
    <link href="<?php echo $css_path; ?>" rel="stylesheet">
    
    <!-- Inline critical styles for 60/40 color scheme -->
    <style>
        :root {
            --bp-primary: #1e3a5f;
            --bp-primary-dark: #0f2744;
            --bp-secondary: #0ca678;
            --bp-secondary-dark: #099268;
        }
        
        /* Sidebar - Dark Navy Blue Gradient */
        .navbar-vertical {
            background: linear-gradient(180deg, #0f2744 0%, #1e3a5f 50%, #2d4a6f 100%) !important;
        }
        
        /* All sidebar text pure white */
        .navbar-vertical .nav-link,
        .navbar-vertical .nav-link-title,
        .navbar-vertical .nav-link-icon,
        .navbar-vertical .nav-link i {
            color: #ffffff !important;
            opacity: 0.9;
        }
        
        /* Sidebar hover state */
        .navbar-vertical .nav-link:hover,
        .navbar-vertical .nav-link:hover .nav-link-title,
        .navbar-vertical .nav-link:hover .nav-link-icon,
        .navbar-vertical .nav-link:hover i {
            color: #ffffff !important;
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Active menu item - Teal accent */
        .navbar-vertical .nav-link.active {
            background: #0ca678 !important;
            color: #ffffff !important;
            opacity: 1;
            border-radius: 8px;
            margin: 2px 8px;
        }
        .navbar-vertical .nav-link.active .nav-link-title,
        .navbar-vertical .nav-link.active .nav-link-icon,
        .navbar-vertical .nav-link.active i {
            color: #ffffff !important;
            opacity: 1;
        }
        
        /* Logout link - keep red but visible */
        .navbar-vertical .nav-link.text-danger,
        .navbar-vertical .nav-link.text-danger .nav-link-title,
        .navbar-vertical .nav-link.text-danger i {
            color: #ff6b6b !important;
        }
        
        /* Brand text */
        .navbar-vertical .navbar-brand,
        .navbar-vertical .navbar-brand span {
            color: #ffffff !important;
        }
        
        /* Stat cards */
        .stat-card.income { border-left: 4px solid #0ca678; }
        .stat-card.expense { border-left: 4px solid #d63939; }
        .stat-card.balance { border-left: 4px solid #1e3a5f; }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%) !important;
            border-color: #1e3a5f !important;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0f2744 0%, #1e3a5f 100%) !important;
            border-color: #0f2744 !important;
        }
        
        /* Colors */
        .text-success, .stat-card.income .h1 { color: #0ca678 !important; }
        .text-primary { color: #1e3a5f !important; }
        .bg-success-lt { background: rgba(12, 166, 120, 0.1) !important; }
        .bg-primary-lt { background: rgba(30, 58, 95, 0.1) !important; }
        .badge.bg-success-lt { background: rgba(12, 166, 120, 0.15) !important; color: #0ca678 !important; }
        .badge.bg-primary-lt { background: rgba(30, 58, 95, 0.15) !important; color: #1e3a5f !important; }
        .avatar.bg-success-lt { background: rgba(12, 166, 120, 0.15) !important; color: #0ca678 !important; }
        .avatar.bg-primary-lt { background: rgba(30, 58, 95, 0.15) !important; color: #1e3a5f !important; }
        
        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="<?php echo Session::isLoggedIn() ? 'layout-fluid' : ''; ?>">
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<?php if (Session::isLoggedIn()): ?>
<div class="page">
    <!-- Sidebar -->
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <h1 class="navbar-brand navbar-brand-autodark">
                <a href="dashboard.php" class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm" style="background: rgba(255,255,255,0.2);">
                        <i class="ti ti-wallet fs-3 text-white"></i>
                    </span>
                    <span class="text-white fw-bold">BudgetPlanner</span>
                </a>
            </h1>
            
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-home"></i>
                            </span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['transactions.php', 'add_transaction.php', 'edit_transaction.php']) ? 'active' : ''; ?>" href="transactions.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-arrows-left-right"></i>
                            </span>
                            <span class="nav-link-title">Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['categories.php', 'add_category.php', 'edit_category.php']) ? 'active' : ''; ?>" href="categories.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-tags"></i>
                            </span>
                            <span class="nav-link-title">Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['budgets.php', 'add_budget.php', 'edit_budget.php']) ? 'active' : ''; ?>" href="budgets.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-pig-money"></i>
                            </span>
                            <span class="nav-link-title">Budgets</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-chart-bar"></i>
                            </span>
                            <span class="nav-link-title">Reports</span>
                        </a>
                    </li>
                    
                    <li class="nav-item mt-auto">
                        <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="nav-link-title">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-logout"></i>
                            </span>
                            <span class="nav-link-title">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    
    <div class="page-wrapper">
        <!-- Header -->
        <header class="page-header d-print-none">
            <div class="container-xl">
                <div class="page-header-content">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <h2 class="page-title">
                                <?php 
                                $titles = [
                                    'dashboard.php' => 'Dashboard',
                                    'transactions.php' => 'Transactions',
                                    'add_transaction.php' => 'Add Transaction',
                                    'edit_transaction.php' => 'Edit Transaction',
                                    'categories.php' => 'Categories',
                                    'add_category.php' => 'Add Category',
                                    'edit_category.php' => 'Edit Category',
                                    'budgets.php' => 'Budgets',
                                    'add_budget.php' => 'Add Budget',
                                    'edit_budget.php' => 'Edit Budget',
                                    'reports.php' => 'Reports',
                                    'profile.php' => 'Profile Settings'
                                ];
                                echo $titles[$current_page] ?? 'BudgetPlanner';
                                ?>
                            </h2>
                        </div>
                        <div class="col-auto ms-auto">
                            <div class="dropdown">
                                <a href="#" class="btn btn-ghost-secondary" data-bs-toggle="dropdown">
                                    <span class="avatar avatar-sm bg-primary-lt text-primary">
                                        <?php echo strtoupper(substr(Session::getUsername(), 0, 1)); ?>
                                    </span>
                                    <span class="d-none d-md-inline ms-2"><?php echo htmlspecialchars(Session::getUsername()); ?></span>
                                    <i class="ti ti-chevron-down ms-1"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="ti ti-user me-2"></i>Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="ti ti-logout me-2"></i>Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">
<?php else: ?>
<!-- Auth Layout -->
<div class="page page-center">
    <div class="container container-tight py-4">
<?php endif; ?>
