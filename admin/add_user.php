<?php
 include "../database/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = $_POST['status'];

    if(empty($username) || empty($email) || empty($password) || empty($role) || empty($status)){
        echo "<script>alert('Please fill all the required fields');</script>";
    } 
 }
 include "../includes/header.php";
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-user-plus';
        $ph_title = 'Add User';
        $ph_subtitle = 'Create a new user account.';
        $ph_back_link = 'view_user.php';
        $ph_back_label = 'Back to Users';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="view_user.php" method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-id-card me-1"></i> Account Details
                            </h6>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold text-dark">Username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="e.g. jdoe" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="e.g. jdoe@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-dark">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter a password" required>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-user-shield me-1"></i> Role &amp; Status
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="role" class="form-label fw-semibold text-dark">Role</label>
                                    <select id="role" name="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        <option value="admin">admin</option>
                                        <option value="Sales">sales</option>
                                        <option value="HR">hr</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label fw-semibold text-dark">Status</label>
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        <option value="active">active</option>
                                        <option value="inactive">inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Add User
                                </button>
                                <a href="view_user.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>