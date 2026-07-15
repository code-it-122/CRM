<?php
 include "../database/db.php";

 if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql="insert into users (name,email,password,role,status) values(?,?,?,?,?)";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $password, $role, $status);
    $result=mysqli_stmt_execute($stmt);

    if($result){
        header("Location: view_user.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }

 }

 $sql="SELECT * FROM users";
 $result=mysqli_query($conn,$sql);
 $total_users = mysqli_num_rows($result);
  include "../includes/header.php";
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-users';
        $ph_title = 'Users';
        $ph_subtitle = $total_users . ' user' . ($total_users == 1 ? '' : 's') . ' registered';
        $ph_action_link = 'add_user.php';
        $ph_action_label = 'Add User';
        $ph_action_icon = 'fa-plus';
        include "../includes/page_header.php";
        ?>

        <?php if ($total_users > 0): ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table-container">
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th colspan="2">Actions</th>
                        </tr>
                        <?php while ($user = mysqli_fetch_assoc($result)):
                            if ($user['status'] == 'active') {
                                $badge = 'bg-success bg-opacity-10 text-success border border-success-subtle';
                                $label = 'Active';
                                $icon = 'fa-circle-check';
                            } else {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';
                                $label = 'Inactive';
                                $icon = 'fa-circle-xmark';
                            }
                        ?>
                        <tr>
                            <td><span class="text-muted">#<?php echo $user['user_id']; ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-2"
                                         style="width: 36px; height: 36px; flex-shrink: 0;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($user['name']); ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($user['email']); ?></span></td>
                            <td><span class="fw-bold text-dark"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-2 py-1">
                                    <i class="fa-solid <?php echo $icon; ?> me-1"></i><?php echo $label; ?>
                                </span>
                            </td>
                            <td class="edit-btn"><a href="edit_user.php?id=<?php echo $user['user_id']; ?>"><i class="fa-solid fa-pen me-1"></i>Edit</a></td>
                            <td class="delete-btn">
                                <a href="delete_user.php?id=<?php echo $user['user_id']; ?>"
                                   onclick="return confirm('Are you sure you want to delete this user?');">
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
                $es_icon = 'fa-users';
                $es_title = 'No users yet';
                $es_desc = 'There are no user accounts yet. Add your first user to get started.';
                $es_action_link = 'add_user.php';
                $es_action_label = 'Add User';
                include "../includes/empty_state.php";
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "../includes/footer.php"; ?>