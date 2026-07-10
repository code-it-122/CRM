<?php
include "../database/db.php";

// Handle POST request from add_attendance.php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = intval($_POST['employee_id']);
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status']; // 'present', 'absent', 'half_day'

    if (empty($employee_id) || empty($attendance_date) || empty($status)) {
        echo "<script>alert('Please fill all the required fields'); window.history.back();</script>";
        exit();
    }

    // Insert into attendance
    $sql = "INSERT INTO attendance (employee_id, attendance_date, status) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $employee_id, $attendance_date, $status);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>
                alert('Attendance marked successfully');
                window.location.href = 'view_attendance.php';
              </script>";
        exit();
    } else {
        die("Error marking attendance: " . mysqli_stmt_error($stmt));
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
        <h1>Attendance</h1>
        <hr>
        <a href="add_attendance.php" class="add-btn">Mark Attendance</a>
        <table class="table-container">
            <tr>
                <th>Attendance ID</th>
                <th>Employee Name</th>
                <th>Attendance Date</th>
                <th>Status</th>
                <th colspan="2">Actions</th>
            </tr>
            <?php
            $sql = "SELECT a.attendance_id, e.name AS employee_name, a.attendance_date, a.status 
                    FROM attendance a
                    JOIN employees e ON a.employee_id = e.employee_id
                    ORDER BY a.attendance_date DESC, a.attendance_id DESC";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // map DB lowercase status value to user-friendly label
                    $display_status = "";
                    if ($row['status'] == 'present') $display_status = "Present";
                    elseif ($row['status'] == 'absent') $display_status = "Absent";
                    elseif ($row['status'] == 'half_day') $display_status = "Half Day";
                    else $display_status = htmlspecialchars($row['status']);

                    echo "<tr>";
                    echo "<td>" . $row['attendance_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['employee_name']) . "</td>";
                    echo "<td>" . $row['attendance_date'] . "</td>";
                    echo "<td>" . $display_status . "</td>";
                    echo "<td class='edit-btn'><a href='edit_attendance.php?id=" . $row['attendance_id'] . "'>Edit</a></td>";
                    echo "<td class='delete-btn'>
                            <a href='delete_attendance.php?id=" . $row['attendance_id'] . "'
                               onclick=\"return confirm('Are you sure you want to delete this attendance record?');\">
                               Delete
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No attendance records found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
