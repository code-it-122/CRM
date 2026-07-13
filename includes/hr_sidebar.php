<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin_sidebar d-flex flex-column h-100">
    <div class="sidebar-header px-4 py-3 border-bottom border-success-subtle mb-3 text-center">
        <h4 class="text-white mb-0 fw-bold"><i class="fa-solid fa-cubes text-warning me-2"></i>CRM System</h4>
        <small class="text-white-50 text-uppercase" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">HR Portal</small>
    </div>
    
    <div class="flex-grow-1">
        <a href="../HR/hr_dashboard.php" class="<?php echo ($current_page == 'hr_dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie me-2"></i> Dashboard
        </a>
        <a href="../HR/view_employee.php" class="<?php echo ($current_page == 'view_employee.php' || $current_page == 'add_employee.php' || $current_page == 'edit_employee.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users-line me-2"></i> Employees
        </a>
        <a href="../HR/view_attendance.php" class="<?php echo ($current_page == 'view_attendance.php' || $current_page == 'add_attendance.php' || $current_page == 'edit_attendance.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-check me-2"></i> Attendance
        </a>
        <a href="../HR/view_leave.php" class="<?php echo ($current_page == 'view_leave.php' || $current_page == 'add_leave.php' || $current_page == 'edit_leave.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-minus me-2"></i> Leaves
        </a>
    </div>

    <div class="p-3 border-top border-success-subtle">
        <form method="POST" action="../auth/logout.php" class="mb-0">
            <button type="submit" class="logout btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center fw-medium border-0 bg-white text-success shadow-sm rounded-3 hover-shadow" style="transition: all 0.2s;">
                <i class="fa-solid fa-right-from-bracket me-2 text-success"></i> Logout
            </button>
        </form>
    </div>
</div>