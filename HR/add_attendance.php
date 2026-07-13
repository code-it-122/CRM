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
        $ph_icon = 'fa-calendar-check';
        $ph_title = 'Add Attendance';
        $ph_subtitle = 'Record employee attendance.';
        $ph_back_link = 'view_attendance.php';
        $ph_back_label = 'Back to Attendance';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="view_attendance.php" method="POST">

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
                                <i class="fa-solid fa-clipboard-check me-1"></i> Attendance Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="attendance_date" class="form-label fw-semibold text-dark">Attendance Date</label>
                                    <input type="date" id="attendance_date" name="attendance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label fw-semibold text-dark">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="present">Present</option>
                                        <option value="absent">Absent</option>
                                        <option value="half_day">Half Day</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Mark Attendance
                                </button>
                                <a href="view_attendance.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>