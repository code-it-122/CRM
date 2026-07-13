<?php
include "../database/db.php";

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_id = intval($_POST['leave_id']);
    $employee_id = intval($_POST['employee_id']);
    $leave_type = trim($_POST['leave_type']);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = trim($_POST['reason']);
    $status = $_POST['status'];

    if (empty($leave_id) || empty($employee_id) || empty($leave_type) || empty($from_date) || empty($to_date) || empty($status)) {
        echo "<script>alert('Please fill all the required fields'); window.history.back();</script>";
        exit();
    }

    $sql = "UPDATE leaves SET employee_id = ?, leave_type = ?, from_date = ?, to_date = ?, reason = ?, status = ? WHERE leave_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssssi", $employee_id, $leave_type, $from_date, $to_date, $reason, $status, $leave_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_leave.php");
        exit();
    } else {
        echo "<script>alert('Error updating leave request');</script>";
    }
}

// Fetch current details
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM leaves WHERE leave_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $leave = mysqli_fetch_assoc($result);

    if (!$leave) {
        die("Leave record not found.");
    }
} else {
    header("Location: view_leave.php");
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
        $ph_title = 'Edit Leave Request';
        $ph_subtitle = 'Update leave request details.';
        $ph_back_link = 'view_leave.php';
        $ph_back_label = 'Back to Leaves';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_leave.php" method="POST">
                            <input type="hidden" name="leave_id" value="<?php echo $leave['leave_id']; ?>">

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
                                            $selected = ($row['employee_id'] == $leave['employee_id']) ? "selected" : "";
                                            echo "<option value='" . $row['employee_id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
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
                                <input type="text" id="leave_type" name="leave_type" class="form-control" value="<?php echo htmlspecialchars($leave['leave_type']); ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_date" class="form-label fw-semibold text-dark">From Date</label>
                                    <input type="date" id="from_date" name="from_date" class="form-control" value="<?php echo $leave['from_date']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="to_date" class="form-label fw-semibold text-dark">To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control" value="<?php echo $leave['to_date']; ?>" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="reason" class="form-label fw-semibold text-dark">Reason</label>
                                <textarea id="reason" name="reason" class="form-control" rows="3"><?php echo htmlspecialchars($leave['reason'] ?? ''); ?></textarea>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-circle-info me-1"></i> Status
                            </h6>
                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold text-dark">Leave Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="pending" <?php if ($leave['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                    <option value="approved" <?php if ($leave['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                                    <option value="rejected" <?php if ($leave['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Save Changes
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