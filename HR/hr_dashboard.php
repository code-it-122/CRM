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

    <div class="view py-4 px-4">
        <!-- Welcome Jumbotron -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold text-success mb-1">HR Dashboard</h3>
                    <p class="text-muted mb-0">Here is an overview of your workforce today.</p>
                </div>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Employees -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-primary border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Total Employees</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $total_employees; ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-id-badge fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-success border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Active Employees</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $active_employees; ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-user-check fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inactive Employees -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-danger border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Inactive Employees</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $inactive_employees; ?></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-user-xmark fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Leaves -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden border-start border-warning border-4 card-animate">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">Pending Leaves</h6>
                            <h3 class="mb-0 text-dark fw-bold"><?php echo $pending_leaves; ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa-solid fa-calendar-minus fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Employees -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-4 pb-0">
                <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i>Recent Employees</h5>
            </div>
            <div class="table-responsive">
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
                        while ($employee = mysqli_fetch_assoc($recent_result)):
                            if ($employee['status'] == 'active') {
                                $badge = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                                $icon = 'fa-circle-check';
                            } else {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                                $icon = 'fa-circle-xmark';
                            }
                    ?>
                        <tr>
                            <td><span class="text-muted">#<?php echo $employee['employee_id']; ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($employee['name']); ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($employee['department']); ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($employee['designation']); ?></span></td>
                            <td><span class="text-muted"><?php echo $employee['joining_date']; ?></span></td>
                            <td>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-2 py-1">
                                    <i class="fa-solid <?php echo $icon; ?> me-1"></i><?php echo ucfirst($employee['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted py-3'>No employees found.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>