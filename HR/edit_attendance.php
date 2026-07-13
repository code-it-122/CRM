<?php
include "../database/db.php";

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendance_id = intval($_POST['attendance_id']);
    $employee_id = intval($_POST['employee_id']);
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    if (empty($attendance_id) || empty($employee_id) || empty($attendance_date) || empty($status)) {
        echo "<script>alert('Please fill all the required fields'); window.history.back();</script>";
        exit();
    }

    $sql = "UPDATE attendance SET employee_id = ?, attendance_date = ?, status = ? WHERE attendance_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $employee_id, $attendance_date, $status, $attendance_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_attendance.php");
        exit();
    } else {
        echo "<script>alert('Error updating attendance record');</script>";
    }
}

// Fetch current details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM attendance WHERE attendance_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $attendance = mysqli_fetch_assoc($result);

    if (!$attendance) {
        die("Attendance record not found.");
    }
} else {
    header("Location: view_attendance.php");
    exit();
}

// Fetch employees for dropdown selection
$employees_result = mysqli_query($conn, "SELECT employee_id, name FROM employees ORDER BY name ASC");

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
        <?php
        $ph_icon = 'fa-pen-to-square';
        $ph_title = 'Edit Attendance';
        $ph_subtitle = 'Update employee attendance record.';
        $ph_back_link = 'view_attendance.php';
        $ph_back_label = 'Back to Attendance';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_attendance.php" method="POST">
                            <input type="hidden" name="attendance_id" value="<?php echo $attendance['attendance_id']; ?>">

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
                                            $selected = ($row['employee_id'] == $attendance['employee_id']) ? "selected" : "";
                                            echo "<option value='" . $row['employee_id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
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
                                    <input type="date" id="attendance_date" name="attendance_date" class="form-control" value="<?php echo $attendance['attendance_date']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label fw-semibold text-dark">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="present" <?php if ($attendance['status'] == 'present') echo 'selected'; ?>>Present</option>
                                        <option value="absent" <?php if ($attendance['status'] == 'absent') echo 'selected'; ?>>Absent</option>
                                        <option value="half_day" <?php if ($attendance['status'] == 'half_day') echo 'selected'; ?>>Half Day</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Save Changes
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