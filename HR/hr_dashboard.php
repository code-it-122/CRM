<?php
include "../database/db.php";

// Fetch counts
$total_employees = 0;
$active_employees = 0;
$inactive_employees = 0;
$pending_leaves = 0;

// Total Employees
$q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees");
if ($r = mysqli_fetch_assoc($q)) {
    $total_employees = $r['total'];
}

// Active Employees
$q = mysqli_query($conn, "SELECT COUNT(*) AS active FROM employees WHERE status = 'active'");
if ($r = mysqli_fetch_assoc($q)) {
    $active_employees = $r['active'];
}

// Inactive Employees
$q = mysqli_query($conn, "SELECT COUNT(*) AS inactive FROM employees WHERE status = 'inactive'");
if ($r = mysqli_fetch_assoc($q)) {
    $inactive_employees = $r['inactive'];
}

// Pending Leave Requests
$q = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM leaves WHERE status = 'pending'");
if ($r = mysqli_fetch_assoc($q)) {
    $pending_leaves = $r['pending'];
}

// Recent Employees
$recent_query = "SELECT * FROM employees ORDER BY joining_date DESC, employee_id DESC LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);

include "../includes/header.php";
?>

<div class="admin-container">
    <?php 
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'hr') {
        include "../includes/hr_sidebar.php";
    }
    ?>
    <div class="view">
        <h1>HR Dashboard</h1>     
        <div class="cards" style="margin-bottom: 30px;">
            <div class="card">
                <h3>Total Employees</h3>
                <p><?php echo $total_employees; ?></p>
            </div>
            <div class="card">
                <h3>Active Employees</h3>
                <p><?php echo $active_employees; ?></p>
            </div>
            <div class="card">
                <h3>Inactive Employees</h3>
                <p><?php echo $inactive_employees; ?></p>
            </div>
            <div class="card">
                <h3>Pending Leaves</h3>
                <p><?php echo $pending_leaves; ?></p>
            </div>
        </div>

        <h2>Recent Employees</h2>
        <table class="table-container">
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Joining Date</th>
                <th>Status</th>
            </tr>
            <?php
            if ($recent_result && mysqli_num_rows($recent_result) > 0) {
                while ($employee = mysqli_fetch_assoc($recent_result)) {
                    echo "<tr>";
                    echo "<td>" . $employee['employee_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($employee['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($employee['department']) . "</td>";
                    echo "<td>" . htmlspecialchars($employee['designation']) . "</td>";
                    echo "<td>" . $employee['joining_date'] . "</td>";
                    echo "<td>" . ucfirst($employee['status']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No employees found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
