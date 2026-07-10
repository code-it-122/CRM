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
        <h1>Apply Leave</h1>
        <form action="view_leave.php" method="POST">
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

            <label for="leave_type">Leave Type:</label>
            <input type="text" id="leave_type" name="leave_type" placeholder="e.g. Sick Leave, Annual Leave" required><br>

            <label for="from_date">From Date:</label>
            <input type="date" id="from_date" name="from_date" required><br>

            <label for="to_date">To Date:</label>
            <input type="date" id="to_date" name="to_date" required><br>

            <label for="reason">Reason:</label>
            <textarea id="reason" name="reason" rows="3" placeholder="Reason for leave"></textarea><br>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="pending" selected>Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select><br>

            <button type="submit">Apply Leave</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
