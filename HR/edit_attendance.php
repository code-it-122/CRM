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
        echo "<script>
                alert('Attendance updated successfully');
                window.location.href = 'view_attendance.php';
              </script>";
        exit();
    } else {
        die("Error updating attendance: " . mysqli_stmt_error($stmt));
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
    <div class="add-user">
        <h1>Edit Attendance</h1>
        <form method="POST">
            <input type="hidden" name="attendance_id" value="<?php echo $attendance['attendance_id']; ?>">
            
            <label for="employee_id">Select Employee:</label>
            <select name="employee_id" id="employee_id" required>
                <option value="">Select Employee</option>
                <?php
                if ($employees_result && mysqli_num_rows($employees_result) > 0) {
                    while ($row = mysqli_fetch_assoc($employees_result)) {
                        $selected = ($row['employee_id'] == $attendance['employee_id']) ? "selected" : "";
                        echo "<option value='" . $row['employee_id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                    }
                }
                ?>
            </select><br>

            <label for="attendance_date">Attendance Date:</label>
            <input type="date" id="attendance_date" name="attendance_date" value="<?php echo $attendance['attendance_date']; ?>" required><br>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="">Select Status</option>
                <option value="present" <?php if($attendance['status'] == 'present') echo 'selected'; ?>>Present</option>
                <option value="absent" <?php if($attendance['status'] == 'absent') echo 'selected'; ?>>Absent</option>
                <option value="half_day" <?php if($attendance['status'] == 'half_day') echo 'selected'; ?>>Half Day</option>
            </select><br>

            <button type="submit">Update Attendance</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
