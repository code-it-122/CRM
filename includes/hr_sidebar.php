
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin_sidebar">
    <a href="../HR/hr_dashboard.php" class="<?php echo ($current_page == 'hr_dashboard.php') ? 'active' : ''; ?>">Dashboard</a><br>
    <a href="../HR/view_employee.php" class="<?php echo ($current_page == 'view_employee.php' || $current_page == 'add_employee.php' || $current_page == 'edit_employee.php') ? 'active' : ''; ?>">Employees</a><br>
    <a href="../HR/view_attendance.php" class="<?php echo ($current_page == 'view_attendance.php' || $current_page == 'add_attendance.php' || $current_page == 'edit_attendance.php') ? 'active' : ''; ?>">Attendance</a><br>
    <a href="../HR/view_leave.php" class="<?php echo ($current_page == 'view_leave.php' || $current_page == 'add_leave.php' || $current_page == 'edit_leave.php') ? 'active' : ''; ?>">Leaves</a><br>
    <br><br>
    <form method="POST" action="../auth/logout.php">
        <button type="submit" class="logout">Logout</button>
    </form>
</div>
