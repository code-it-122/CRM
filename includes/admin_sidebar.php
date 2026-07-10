<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin_sidebar">
    <a href="../admin/admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>"> Admin Dashboard</a><br>
    <a href="../sales/sales_dashboard.php" class="<?php echo ($current_page == 'sales_dashboard.php') ? 'active' : ''; ?>">Sales Dashboard</a><br>
    <a href="../HR/hr_dashboard.php" class="<?php echo ($current_page == 'hr_dashboard.php') ? 'active' : ''; ?>">HR Dashboard</a><br>
    <a href="../admin/view_user.php"  class="<?php echo ($current_page == 'view_user.php') ? 'active' : ''; ?>"> Users</a><br>
    <a href="../products/view_product.php" class="<?php echo ($current_page == 'view_product.php') ? 'active' : ''; ?>"> Products</a><br>
    <a href="../customers/view_customer.php" class="<?php echo ($current_page == 'view_customer.php') ? 'active' : ''; ?>"> Customers</a><br>
    <a href="../sales/view_sales.php" class="<?php echo ($current_page == 'view_sales.php') ? 'active' : ''; ?>"> Sales</a><br>
    <a href="../billing/view_invoice.php" class="<?php echo ($current_page == 'view_invoice.php') ? 'active' : ''; ?>"> Billing</a><br>
    <a href="../HR/view_employee.php" class="<?php echo ($current_page == 'view_employee.php' || $current_page == 'add_employee.php' || $current_page == 'edit_employee.php') ? 'active' : ''; ?>">Employees</a><br>
    <a href="../HR/view_attendance.php" class="<?php echo ($current_page == 'view_attendance.php' || $current_page == 'add_attendance.php' || $current_page == 'edit_attendance.php') ? 'active' : ''; ?>">Attendance</a><br>
    <a href="../HR/view_leave.php" class="<?php echo ($current_page == 'view_leave.php' || $current_page == 'add_leave.php' || $current_page == 'edit_leave.php') ? 'active' : ''; ?>">Leaves</a><br>
    <a href="../reports/generate_reports.php"> Reports</a><br> 
<div><br><br>
<form method="POST" action="../auth/logout.php">
<button type="submit" name="logout" class="logout">Logout</button>
</form>
</div>
</div>