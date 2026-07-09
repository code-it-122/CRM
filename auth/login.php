<?php
  session_start();
 include "../database/db.php";
 include "../includes/header.php";


 if(isset($_SESSION['user_id'])) {
    if($_SESSION['role']=='admin'){
        header("location: ../admin/admin_dashboard.php");
        exit();
    }
    if($_SESSION['role']=='sales'){
        header("location: ../sales/sales_dashboard.php");
        exit();
    }
    if($_SESSION['role']=='hr'){
        header("location: ../hr/hr_dashboard.php");
        exit();
        }       
}

$errors =[];
if($_SERVER['REQUEST_METHOD']=='POST'){
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($role) || empty($email) || empty($password)){
        $errors[] = "Please Fill the Required fields";
        echo "<script>alert('Please Fill the Required fields');</script>";
}

if(empty($errors)){
    $sql="select * from users where email =? AND role=? LIMIT 1";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "ss" ,$email ,$role);
    mysqli_stmt_execute($stmt);
    $result=mysqli_stmt_get_result($stmt);
   

   if ($result && mysqli_num_rows($result) > 0) {

    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($_SESSION['role'] == 'admin') {
            header("Location: ../admin/admin_dashboard.php");
            exit();
        }
        elseif ($_SESSION['role'] == 'sales') {
            header("Location: ../sales/sales_dashboard.php");
            exit();
        }
        elseif ($_SESSION['role'] == 'hr') {
            header("Location: ../hr/hr_dashboard.php");
            exit();
        }

    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }

} else {
    echo "<script>alert('Invalid Email or Role');</script>";
}
    }
}
?>
<div class="login-container">
    <form method="POST">
        <h2>Login</h2><hr>
        <div>
        <label for="role">Role :</label>
        <select id="role" name="role">
        <option>Select role</option>    
        <option value="admin">Admin</option>
            <option value="sales">Sales</option>
            <option value="hr">HR</option>
            </select><br><br>
            <label>email :</label>
            <input type="email" name="email" placeholder="Enter your email" required><br><br>
            <label>Password :</label> 
            <input type="password" name="password" placeholder="Enter your password" required><br><br>
            <button type="submit">Login</button>
            </div>
</form>
</div>
<?php include "../includes/footer.php"; ?>