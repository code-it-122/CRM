<?php
include "../includes/header.php";
include "../database/db.php";

// Fetch employees for dropdown selection
$employees_result = mysqli_query($conn, "SELECT employee_id, name FROM employees ORDER BY name ASC");
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
        <?php
        $ph_icon = 'fa-plane-departure';
        $ph_title = 'Apply Leave';
        $ph_subtitle = 'Submit a leave request for an employee.';
        $ph_back_link = 'view_leave.php';
        $ph_back_label = 'Back to Leaves';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="view_leave.php" method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-user me-1"></i> Employee
                            </h6>
                            <div class="mb-4">
                                <label for="employee_id" class="form-label fw-semibold text-dark">Select Employee</label>
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">Select Employee</option>
                                    <?php
                                    if ($employees_result && mysqli_num_rows($employees_result) > 0) {
                                        while ($row = mysqli_fetch_assoc($employees_result)) {
                                            echo "<option value='" . $row['employee_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-calendar-days me-1"></i> Leave Details
                            </h6>
                            <div class="mb-3">
                                <label for="leave_type" class="form-label fw-semibold text-dark">Leave Type</label>
                                <input type="text" id="leave_type" name="leave_type" class="form-control" placeholder="e.g. Sick Leave, Annual Leave" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_date" class="form-label fw-semibold text-dark">From Date</label>
                                    <input type="date" id="from_date" name="from_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_date" class="form-label fw-semibold text-dark">To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="reason" class="form-label fw-semibold text-dark">Reason</label>
                                <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Reason for leave"></textarea>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-circle-info me-1"></i> Status
                            </h6>
                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold text-dark">Leave Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="pending" selected>Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Apply Leave
                                </button>
                                <a href="view_leave.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>