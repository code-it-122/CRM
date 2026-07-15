<?php
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $source = trim($_POST['source']);
    $requirement = trim($_POST['requirement']);
    $status = $_POST['status'];
    $follow_up_date = $_POST['follow_up_date'] ?: null;
    $notes = trim($_POST['notes']);

    if (empty($name)) {
        echo "<script>alert('Lead name is required');</script>";
    } else {
        $sql = "INSERT INTO leads (name, phone, email, source, requirement, status, follow_up_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssss", $name, $phone, $email, $source, $requirement, $status, $follow_up_date, $notes);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "<script>
                    alert('Lead added successfully');
                    window.location.href = 'view_leads.php';
                  </script>";
            exit();
        } else {
            die("Error: " . mysqli_stmt_error($stmt));
        }
    }
}
include "../includes/header.php";
?>

<div class="admin-container">
    <?php
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
    ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-bullseye';
        $ph_title = 'Add Lead';
        $ph_subtitle = 'Track a new prospect.';
        $ph_back_link = 'view_leads.php';
        $ph_back_label = 'Back to Leads';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-id-card me-1"></i> Contact Information
                            </h6>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-dark">Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. John Smith" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold text-dark">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. 9876543210">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com">
                                </div>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-clipboard-list me-1"></i> Lead Details
                            </h6>
                            <div class="mb-3">
                                <label for="source" class="form-label fw-semibold text-dark">Source</label>
                                <input type="text" id="source" name="source" class="form-control" placeholder="e.g. Website, Referral, Cold Call">
                            </div>
                            <div class="mb-3">
                                <label for="requirement" class="form-label fw-semibold text-dark">Requirement</label>
                                <textarea id="requirement" name="requirement" class="form-control" rows="2" placeholder="What is the prospect looking for?"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label fw-semibold text-dark">Status</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="new" selected>New</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="interested">Interested</option>
                                        <option value="converted">Converted</option>
                                        <option value="lost">Lost</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="follow_up_date" class="form-label fw-semibold text-dark">Follow-up Date</label>
                                    <input type="date" id="follow_up_date" name="follow_up_date" class="form-control">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="form-label fw-semibold text-dark">Notes</label>
                                <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Any additional notes"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Add Lead
                                </button>
                                <a href="view_leads.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>