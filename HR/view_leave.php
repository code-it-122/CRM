<?php

include "../database/db.php";

// Handle POST request from add_leave.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $leave_type = trim($_POST['leave_type']);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = trim($_POST['reason']);
    $status = $_POST['status']; // 'pending', 'approved', 'rejected'

    if (empty($employee_id) || empty($leave_type) || empty($from_date) || empty($to_date) || empty($status)) {
        echo "<script>alert('Please fill all the required fields'); window.history.back();</script>";
        exit();
    }

    // Insert into leaves
    $sql = "INSERT INTO leaves (employee_id, leave_type, from_date, to_date, reason, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssss", $employee_id, $leave_type, $from_date, $to_date, $reason, $status);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>
                alert('Leave applied successfully');
                window.location.href = 'view_leave.php';
              </script>";
        exit();
    } else {
        die("Error applying for leave: " . mysqli_stmt_error($stmt));
    }
}

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
        <h1>Leaves</h1>
        <hr>
        <a href="add_leave.php" class="add-btn">Apply Leave</a>
        <table class="table-container">
            <tr>
                <th>Leave ID</th>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th colspan="2">Actions</th>
            </tr>
            <?php
            $sql = "SELECT l.leave_id, e.name AS employee_name, l.leave_type, l.from_date, l.to_date, l.reason, l.status 
                    FROM leaves l
                    JOIN employees e ON l.employee_id = e.employee_id
                    ORDER BY l.from_date DESC, l.leave_id DESC";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // map DB lowercase status value to user-friendly label
                    $display_status = "";
                    if ($row['status'] == 'pending') $display_status = "Pending";
                    elseif ($row['status'] == 'approved') $display_status = "Approved";
                    elseif ($row['status'] == 'rejected') $display_status = "Rejected";
                    else $display_status = htmlspecialchars($row['status']);

                    echo "<tr>";
                    echo "<td>" . $row['leave_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['employee_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['leave_type']) . "</td>";
                    echo "<td>" . $row['from_date'] . "</td>";
                    echo "<td>" . $row['to_date'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['reason'] ?? '') . "</td>";
                    echo "<td>" . $display_status . "</td>";
                    echo "<td class='edit-btn'><a href='edit_leave.php?id=" . $row['leave_id'] . "'>Edit</a></td>";
                    echo "<td class='delete-btn'>
                            <a href='delete_leave.php?id=" . $row['leave_id'] . "'
                               onclick=\"return confirm('Are you sure you want to delete this leave record?');\">
                               Delete
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No leave records found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
