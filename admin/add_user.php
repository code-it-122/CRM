<?php
 include "../includes/header.php";
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

?>

<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Add User</h1>
        <form action="view_user.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="">Select Role</option>
                <option value="admin">admin</option>
                <option value="Sales">sales</option>
                <option value="HR">hr</option>
            </select><br><br>
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="active">active</option>
                <option value="inactive">inactive</option>
</select><br>
            <button type="submit">Add User</button>
        </form>
    </div>
