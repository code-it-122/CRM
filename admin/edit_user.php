<?php
 include "../includes/header.php";
 include "../database/db.php";

 if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id=$_GET['id'];   
     $sql = "SELECT * FROM users WHERE user_id=?";
     $stmt=mysqli_prepare($conn,$sql);
     mysqli_stmt_bind_param($stmt,"i",$id);
     mysqli_stmt_execute($stmt);
     $result=mysqli_stmt_get_result($stmt);
     $user=mysqli_fetch_assoc($result);

     $name=$user['name'];
     $email=$user['email'];
     $password=$user['password'];
     $role=$user['role'];
     $status=$user['status'];     
 }


?>

<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Edit User</h1>
        <form action="edit_user.php" method="POST">
            <input type="hidden" name="user_id"
       value="<?php echo $user['user_id']; ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="
            <?php echo $name ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="
            <?php echo $email ?>" required><br>

            <label for="role">Role:</label>
           <select name="role" required>
    <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>
        Admin
    </option>

    <option value="employee" <?php if($user['role'] == 'employee') echo 'selected'; ?>>
        Employee
    </option>

    <option value="customer" <?php if($user['role'] == 'customer') echo 'selected'; ?>>
        Customer
    </option>
</select>
            </select><br><br>
            <label for="status">Status:</label>
           <select name="status" required>
    <option value="active" <?php if($user['status'] == 'active') echo 'selected'; ?>>
        Active
    </option>

    <option value="inactive" <?php if($user['status'] == 'inactive') echo 'selected'; ?>>
        Inactive
    </option>
</select><br>
            <button type="submit">Update User</button>
        </form>
    </div>
    <?php
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
        echo "Error: " . mysqli_stmt_error($stmt);
    }
}
    ?>
