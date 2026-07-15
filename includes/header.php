<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assests/css/style.css?v=2">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light" style="font-family: 'Inter', sans-serif;">

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top top-navbar py-2 px-4 shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            
            <!-- Mobile Toggle & Page Title -->
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-3 border shadow-sm" id="sidebar-toggle" style="border-radius: 6px;">
                    <i class="fa-solid fa-bars text-success"></i>
                </button>
                <div class="d-flex flex-column">
                    <span class="navbar-brand mb-0 h2 text-success fw-bold text-uppercase" style="font-size: 1.15rem; letter-spacing: 0.5px;">
                        <?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?>
                    </span>
                    <nav aria-label="breadcrumb" class="d-none d-sm-block">
                        <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                            <li class="breadcrumb-item"><a href="#" class="text-secondary text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item active text-success" aria-current="page"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <!-- User Actions & Profile -->
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex flex-column text-end me-2 d-none d-md-flex">
                    <span class="text-dark fw-bold" style="font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                    <span class="text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.3px;">
                        <?php echo htmlspecialchars($_SESSION['role'] ?? 'Staff'); ?>
                    </span>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1.5 me-2 text-uppercase d-none d-sm-inline-block" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-shield-halved me-1"></i><?php echo htmlspecialchars($_SESSION['role'] ?? 'Staff'); ?>
                </span>
                
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; font-size: 1.1rem; font-weight: bold;">
                            <?php echo strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)); ?>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 py-2">
                        <li class="px-3 py-2 border-bottom mb-2 d-md-none">
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></div>
                            <small class="text-muted text-uppercase"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Staff'); ?></small>
                        </li>
                        <li>
                            <form method="POST" action="../auth/logout.php" class="mb-0">
                                <button type="submit" name="logout" class="dropdown-item text-danger py-2 d-flex align-items-center fw-medium">
                                    <i class="fa-solid fa-right-from-bracket me-2 text-danger"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div style="margin-top: 75px;"></div>
<?php endif; ?>