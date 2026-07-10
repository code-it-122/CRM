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
        echo "<script>
                alert('Leave request updated successfully');
                window.location.href = 'view_leave.php';
              </script>";
        exit();
    } else {
        die("Error updating leave request: " . mysqli_stmt_error($stmt));
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
    <div class="add-user">
        <h1>Edit Leave Request</h1>
        <form method="POST">
            <input type="hidden" name="leave_id" value="<?php echo $leave['leave_id']; ?>">
            
            <label for="employee_id">Select Employee:</label>
            <select name="employee_id" id="employee_id" required>
                <option value="">Select Employee</option>
                <?php
                if ($employees_result && mysqli_num_rows($employees_result) > 0) {
                    while ($row = mysqli_fetch_assoc($employees_result)) {
                        $selected = ($row['employee_id'] == $leave['employee_id']) ? "selected" : "";
                        echo "<option value='" . $row['employee_id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                    }
                }
                ?>
            </select><br>

            <label for="leave_type">Leave Type:</label>
            <input type="text" id="leave_type" name="leave_type" value="<?php echo htmlspecialchars($leave['leave_type']); ?>" required><br>

            <label for="from_date">From Date:</label>
            <input type="date" id="from_date" name="from_date" value="<?php echo $leave['from_date']; ?>" required><br>

            <label for="to_date">To Date:</label>
            <input type="date" id="to_date" name="to_date" value="<?php echo $leave['to_date']; ?>" required><br>

            <label for="reason">Reason:</label>
            <textarea id="reason" name="reason" rows="3"><?php echo htmlspecialchars($leave['reason'] ?? ''); ?></textarea><br>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="pending" <?php if($leave['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="approved" <?php if($leave['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                <option value="rejected" <?php if($leave['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
            </select><br>

            <button type="submit">Update Leave Request</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
