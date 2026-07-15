<?php
include "../includes/header.php";
include "../database/db.php";
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
        $ph_icon = 'fa-user-plus';
        $ph_title = 'Add Employee';
        $ph_subtitle = 'Create a new employee record.';
        $ph_back_link = 'view_employee.php';
        $ph_back_label = 'Back to Employees';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="view_employee.php" method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-id-card me-1"></i> Personal Information
                            </h6>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-dark">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Jane Doe" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold text-dark">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. 9876543210" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
                                </div>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-building me-1"></i> Job Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label fw-semibold text-dark">Department</label>
                                    <input type="text" id="department" name="department" class="form-control" placeholder="e.g. Sales" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="designation" class="form-label fw-semibold text-dark">Designation</label>
                                    <input type="text" id="designation" name="designation" class="form-control" placeholder="e.g. Sales Executive" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="joining_date" class="form-label fw-semibold text-dark">Joining Date</label>
                                <input type="date" id="joining_date" name="joining_date" class="form-control" required>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-coins me-1"></i> Compensation &amp; Status
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="salary" class="form-label fw-semibold text-dark">Salary (Rs)</label>
                                    <input type="number" step="0.01" min="0" id="salary" name="salary" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label fw-semibold text-dark">Status</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Add Employee
                                </button>
                                <a href="view_employee.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>