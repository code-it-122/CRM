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
        header("Location: view_leave.php");
        exit();
    } else {
        die("Error applying for leave: " . mysqli_stmt_error($stmt));
    }
}

$sql = "SELECT l.leave_id, e.name AS employee_name, l.leave_type, l.from_date, l.to_date, l.reason, l.status
        FROM leaves l
        JOIN employees e ON l.employee_id = e.employee_id
        ORDER BY l.from_date DESC, l.leave_id DESC";
$result = mysqli_query($conn, $sql);
$total_records = mysqli_num_rows($result);
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
        $ph_icon = 'fa-plane-departure';
        $ph_title = 'Leaves';
        $ph_subtitle = $total_records . ' request' . ($total_records == 1 ? '' : 's') . ' on file';
        $ph_action_link = 'add_leave.php';
        $ph_action_label = 'Apply Leave';
        $ph_action_icon = 'fa-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_records > 0): ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
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
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            switch ($row['status']) {
                                case 'approved':
                                    $badge = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                                    $label = 'Approved';
                                    $icon = 'fa-circle-check';
                                    break;
                                case 'rejected':
                                    $badge = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                                    $label = 'Rejected';
                                    $icon = 'fa-circle-xmark';
                                    break;
                                case 'pending':
                                    $badge = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle';
                                    $label = 'Pending';
                                    $icon = 'fa-clock';
                                    break;
                                default:
                                    $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle';
                                    $label = htmlspecialchars($row['status']);
                                    $icon = 'fa-circle-question';
                            }
                        ?>
                        <tr>
                            <td><?php echo $row['leave_id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['employee_name']); ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($row['leave_type']); ?></span></td>
                            <td><span class="text-muted"><?php echo $row['from_date']; ?></span></td>
                            <td><span class="text-muted"><?php echo $row['to_date']; ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($row['reason'] ?? '') ?: '—'; ?></span></td>
                            <td>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-2 py-1">
                                    <i class="fa-solid <?php echo $icon; ?> me-1"></i><?php echo $label; ?>
                                </span>
                            </td>
                            <td class="edit-btn"><a href="edit_leave.php?id=<?php echo $row['leave_id']; ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td>
                            <td class="delete-btn">
                                <a href="delete_leave.php?id=<?php echo $row['leave_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this leave record?');">
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
                $es_icon = 'fa-plane-departure';
                $es_title = 'No leave requests yet';
                $es_desc = 'No leave has been requested. Apply for leave to start tracking employee time off.';
                $es_action_link = 'add_leave.php';
                $es_action_label = 'Apply Leave';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>