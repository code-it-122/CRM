<?php
 include "../includes/header.php";
 include "../database/db.php";

 if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
     header("Location: ../auth/login.php");
     exit();
 }

 if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id=$_GET['id'];   
     $sql = "SELECT * FROM users WHERE user_id=?";
     $stmt=mysqli_prepare($conn,$sql);
     mysqli_stmt_bind_param($stmt,"i",$id);
     mysqli_stmt_execute($stmt);
     $result=mysqli_stmt_get_result($stmt);
     $user=mysqli_fetch_assoc($result);

     if (!$user) {
         echo "<script>alert('User not found.'); window.location.href='view_user.php';</script>";
         exit();
     }

     $name=$user['name'];
     $email=$user['email'];
     $password=$user['password'];
     $role=$user['role'];
     $status=$user['status'];     
 }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = $_POST['user_id'];
    $name = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "UPDATE users
            SET name = ?, email = ?, role = ?, status = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "ssssi",
        $name,
        $email,
        $role,
        $status,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_user.php");
        exit();
    } else {
        echo "<script>alert('Error updating user.');</script>";
    }
}
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-user-pen';
        $ph_title = 'Edit User';
        $ph_subtitle = 'Update account details and permissions.';
        $ph_back_link = 'view_user.php';
        $ph_back_label = 'Back to Users';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_user.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-id-card me-1"></i> Account Details
                            </h6>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold text-dark">Username</label>
                                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-user-shield me-1"></i> Role &amp; Status
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="role" class="form-label fw-semibold text-dark">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        <option value="sales" <?php if($user['role'] == 'sales') echo 'selected'; ?>>Sales</option>
                                        <option value="hr" <?php if($user['role'] == 'hr') echo 'selected'; ?>>HR</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="status" class="form-label fw-semibold text-dark">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" <?php if($user['status'] == 'active') echo 'selected'; ?>>Active</option>
                                        <option value="inactive" <?php if($user['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Save Changes
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