<?php
include "../includes/header.php";
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
        header("Location: view_attendance.php");
        exit();
    } else {
        die("Error marking attendance: " . mysqli_stmt_error($stmt));
    }
}

$sql = "SELECT a.attendance_id, e.name AS employee_name, a.attendance_date, a.status
        FROM attendance a
        JOIN employees e ON a.employee_id = e.employee_id
        ORDER BY a.attendance_date DESC, a.attendance_id DESC";
$result = mysqli_query($conn, $sql);
$total_records = mysqli_num_rows($result);
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
        $ph_title = 'Attendance';
        $ph_subtitle = $total_records . ' record' . ($total_records == 1 ? '' : 's') . ' logged';
        $ph_action_link = 'add_attendance.php';
        $ph_action_label = 'Mark Attendance';
        $ph_action_icon = 'fa-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_records > 0): ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table-container">
                        <tr>
                            <th>Attendance ID</th>
                            <th>Employee Name</th>
                            <th>Attendance Date</th>
                            <th>Status</th>
                            <th colspan="2">Actions</th>
                        </tr>
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            switch ($row['status']) {
                                case 'present':
                                    $badge = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                                    $label = 'Present';
                                    $icon = 'fa-circle-check';
                                    break;
                                case 'absent':
                                    $badge = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                                    $label = 'Absent';
                                    $icon = 'fa-circle-xmark';
                                    break;
                                case 'half_day':
                                    $badge = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle';
                                    $label = 'Half Day';
                                    $icon = 'fa-clock';
                                    break;
                                default:
                                    $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle';
                                    $label = htmlspecialchars($row['status']);
                                    $icon = 'fa-circle-question';
                            }
                        ?>
                        <tr>
                            <td><?php echo $row['attendance_id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['employee_name']); ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted"><?php echo $row['attendance_date']; ?></span></td>
                            <td>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-2 py-1">
                                    <i class="fa-solid <?php echo $icon; ?> me-1"></i><?php echo $label; ?>
                                </span>
                            </td>
                            <td class="edit-btn"><a href="edit_attendance.php?id=<?php echo $row['attendance_id']; ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td>
                            <td class="delete-btn">
                                <a href="delete_attendance.php?id=<?php echo $row['attendance_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this attendance record?');">
                                    <i class="fa-solid fa-trash me-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-calendar-check';
                $es_title = 'No attendance records yet';
                $es_desc = 'No attendance has been logged. Mark attendance to start tracking employee presence.';
                $es_action_link = 'add_attendance.php';
                $es_action_label = 'Mark Attendance';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>