<?php
session_start();
include "../database/db.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("location: ../admin/admin_dashboard.php");
        exit();
    }
    if ($_SESSION['role'] == 'sales') {
        header("location: ../sales/sales_dashboard.php");
        exit();
    }
    if ($_SESSION['role'] == 'hr') {
        header("location: ../hr/hr_dashboard.php");
        exit();
    }       
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($role) || empty($email) || empty($password)) {
        $errors[] = "Please Fill the Required fields";
        echo "<script>alert('Please Fill the Required fields');</script>";
    }

    if (empty($errors)) {
        $sql = "select * from users where email =? AND role=? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss" ,$email ,$role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
       
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
include "../includes/header.php";
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card border-0 shadow rounded-3 p-4 p-sm-5" style="max-width: 450px; width: 100%;">
        <div class="text-center mb-4">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="fa-solid fa-cubes fa-2xl"></i>
            </div>
            <h3 class="fw-bold text-success mb-1">Welcome Back</h3>
            <p class="text-muted small">Please login to your account to continue</p>
        </div>
        
        <form method="POST">
            <!-- Role Selection -->
            <div class="mb-3">
                <label for="role" class="form-label fw-semibold text-dark small">Select User Role</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user-shield"></i></span>
                    <select id="role" name="role" class="form-select bg-light border-start-0 ps-0" required>
                        <option value="">Choose your workspace...</option>    
                        <option value="admin">Admin</option>
                        <option value="sales">Sales Agent</option>
                        <option value="hr">HR Manager</option>
                    </select>
                </div>
            </div>
            
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-dark small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" id="email" name="email" class="form-control bg-light border-start-0 ps-0" placeholder="name@company.com" required>
                </div>
            </div>
            
            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-dark small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control bg-light border-start-0 ps-0" placeholder="••••••••" required>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold shadow-sm" style="background-color: var(--primary-green); border: none; border-radius: 6px;">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Log In
            </button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>