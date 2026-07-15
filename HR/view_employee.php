<?php

include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $designation = trim($_POST['designation']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $salary = floatval($_POST['salary']);
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];

    if (empty($name) || empty($department) || empty($designation) || empty($email) || empty($phone) || empty($joining_date) || empty($status)) {
        echo "<script>alert('Please fill all the required fields'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO employees (name, department, designation, phone, email, salary, joining_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssdss", $name, $department, $designation, $phone, $email, $salary, $joining_date, $status);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>
                alert('Employee added successfully');
                window.location.href = 'view_employee.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
}

include "../includes/header.php";

$sql = "SELECT * FROM employees ORDER BY employee_id DESC";
$result = mysqli_query($conn, $sql);
$total_employees = mysqli_num_rows($result);
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
        $ph_icon = 'fa-users-line';
        $ph_title = 'Employees';
        $ph_subtitle = $total_employees . ' employee' . ($total_employees == 1 ? '' : 's') . ' on record';
        $ph_action_link = 'add_employee.php';
        $ph_action_label = 'Add Employee';
        $ph_action_icon = 'fa-user-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_employees > 0): ?>
            <!-- Search -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body py-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="employeeSearch" class="form-control bg-light border-start-0 ps-0"
                               placeholder="Search employees by name, department or designation...">
                    </div>
                </div>
            </div>

            <!-- Employee Cards -->
            <div class="row g-4" id="employeeGrid">
                <?php while ($employee = mysqli_fetch_assoc($result)):
                    $initial = strtoupper(substr(trim($employee['name']), 0, 1));
                    $searchBlob = strtolower($employee['name'] . ' ' . $employee['department'] . ' ' . $employee['designation']);
                    $is_active = $employee['status'] == 'active';
                ?>
                <div class="col-md-6 col-lg-4 employee-card-wrap" data-search="<?php echo htmlspecialchars($searchBlob); ?>">
                    <div class="card border-0 shadow-sm rounded-3 h-100 card-animate">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center overflow-hidden">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold me-3"
                                         style="width: 48px; height: 48px; font-size: 1.1rem; flex-shrink: 0;">
                                        <?php echo $initial ?: '?'; ?>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate"><?php echo htmlspecialchars($employee['name']); ?></h6>
                                        <small class="text-muted text-truncate d-block"><?php echo htmlspecialchars($employee['designation']); ?></small>
                                    </div>
                                </div>
                                <span class="badge <?php echo $is_active ? 'bg-success bg-opacity-10 text-success border border-success-subtle' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle'; ?> rounded-pill px-2 py-1 flex-shrink-0">
                                    <i class="fa-solid <?php echo $is_active ? 'fa-circle-check' : 'fa-circle-minus'; ?> me-1"></i><?php echo ucfirst($employee['status']); ?>
                                </span>
                            </div>

                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-building text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($employee['department']); ?></span>
                            </div>
                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-phone text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($employee['phone']); ?></span>
                            </div>
                            <div class="mb-2 text-truncate">
                                <i class="fa-solid fa-envelope text-success me-2" style="width: 16px;"></i>
                                <span class="text-dark small"><?php echo htmlspecialchars($employee['email']); ?></span>
                            </div>
                            <div class="mb-3 text-truncate">
                                <i class="fa-solid fa-calendar-days text-success me-2" style="width: 16px;"></i>
                                <span class="text-muted small">Joined <?php echo date('d M Y', strtotime($employee['joining_date'])); ?></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-2">
                                <small class="text-muted">Salary</small>
                                <span class="fw-bold text-success">Rs<?php echo number_format($employee['salary'], 2); ?></span>
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <a href="edit_employee.php?id=<?php echo $employee['employee_id']; ?>" class="btn btn-sm btn-outline-success flex-fill">
                                    <i class="fa-solid fa-pen me-1"></i> Edit
                                </a>
                                <a href="delete_employee.php?id=<?php echo $employee['employee_id']; ?>" class="btn btn-sm btn-outline-danger flex-fill"
                                   onclick="return confirm('Are you sure you want to delete this employee?');">
                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div id="noResults" class="text-center py-5 d-none">
                <i class="fa-solid fa-magnifying-glass text-muted opacity-25 mb-3" style="font-size: 3rem;"></i>
                <p class="text-muted mb-0">No employees match your search.</p>
            </div>

        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-3">
                <?php
                $es_icon = 'fa-users-line';
                $es_title = 'No employees yet';
                $es_desc = 'Start building your team by adding your first employee record.';
                $es_action_link = 'add_employee.php';
                $es_action_label = 'Add Employee';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('employeeSearch');
    if (!searchInput) return;
    const cards = document.querySelectorAll('.employee-card-wrap');
    const noResults = document.getElementById('noResults');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;
        cards.forEach(card => {
            const match = card.getAttribute('data-search').includes(query);
            card.classList.toggle('d-none', !match);
            if (match) visibleCount++;
        });
        noResults.classList.toggle('d-none', visibleCount !== 0);
    });
});
</script>

<?php include "../includes/footer.php"; ?>