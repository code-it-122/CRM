<?php


include "../database/db.php";

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
        <h1>Mark Attendance</h1>
        <form action="view_attendance.php" method="POST">
            <label for="employee_id">Select Employee:</label>
            <select name="employee_id" id="employee_id" required>
                <option value="">Select Employee</option>
                <?php
                if ($employees_result && mysqli_num_rows($employees_result) > 0) {
                    while ($row = mysqli_fetch_assoc($employees_result)) {
                        echo "<option value='" . $row['employee_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                }
                ?>
            </select><br>

            <label for="attendance_date">Attendance Date:</label>
            <input type="date" id="attendance_date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" required><br>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="">Select Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="half_day">Half Day</option>
            </select><br>

            <button type="submit">Mark Attendance</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
