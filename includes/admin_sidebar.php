<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin_sidebar d-flex flex-column h-100">
    <div class="sidebar-header px-4 py-3 border-bottom border-success-subtle mb-3 text-center">
        <h4 class="text-white mb-0 fw-bold"><i class="fa-solid fa-cubes text-warning me-2"></i>CRM System</h4>
        <small class="text-white-50 text-uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">Corporate Suite</small>
    </div>
    
    <div class="flex-grow-1">
        <a href="../admin/admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie me-2"></i> Dashboard
        </a>
        <a href="../admin/view_user.php" class="<?php echo ($current_page == 'view_user.php' || $current_page == 'add_user.php' || $current_page == 'edit_user.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-gear me-2"></i> Users
        </a>
        <a href="../products/view_product.php" class="<?php echo ($current_page == 'view_product.php' || $current_page == 'add_product.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-box-open me-2"></i> Products
        </a>
        <a href="../customers/view_customer.php" class="<?php echo ($current_page == 'view_customer.php' || $current_page == 'add_customer.php' || $current_page == 'edit_customer.php' || $current_page == 'view_customer_details.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-handshake me-2"></i> Customers
        </a>
        <a href="../leads/view_leads.php" class="<?php echo ($current_page == 'view_leads.php' || $current_page == 'add_lead.php' || $current_page == 'edit_lead.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-bullseye me-2"></i> Leads
        </a>
        <a href="../sales/view_sales.php" class="<?php echo ($current_page == 'view_sales.php' || $current_page == 'add_sales.php' || $current_page == 'edit_sales.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-money-bill-trend-up me-2"></i> Sales
        </a>
        <a href="../billing/view_invoice.php" class="<?php echo ($current_page == 'view_invoice.php' || $current_page == 'add_invoice.php' || $current_page == 'edit_invoice.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i> Billing
        </a>
        <a href="../HR/view_employee.php" class="<?php echo ($current_page == 'view_employee.php' || $current_page == 'add_employee.php' || $current_page == 'edit_employee.php' || $current_page == 'view_attendance.php' || $current_page == 'add_attendance.php' || $current_page == 'edit_attendance.php' || $current_page == 'view_leave.php' || $current_page == 'add_leave.php' || $current_page == 'edit_leave.php' || $current_page == 'hr_dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users-line me-2"></i> HR Management
        </a>
        <a href="../reports/generate_reports.php" class="<?php echo ($current_page == 'generate_reports.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-shield me-2"></i> Reports
        </a> 
    </div>

    <div class="p-3 border-top border-success-subtle">
        <form method="POST" action="../auth/logout.php" class="mb-0">
            <button type="submit" name="logout" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center fw-medium border-0 bg-white text-success shadow-sm rounded-3 hover-shadow" style="transition: all 0.2s;">
                <i class="fa-solid fa-right-from-bracket me-2 text-success"></i> Logout
            </button>
        </form>
    </div>
</div>