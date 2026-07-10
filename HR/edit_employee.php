<?php

include "../database/db.php";

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'];
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

    $sql = "UPDATE employees SET name = ?, department = ?, designation = ?, phone = ?, email = ?, salary = ?, joining_date = ?, status = ? WHERE employee_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssdssi", $name, $department, $designation, $phone, $email, $salary, $joining_date, $status, $employee_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Employee updated successfully');
                window.location.href = 'view_employee.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
}

// Fetch current details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM employees WHERE employee_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $employee = mysqli_fetch_assoc($result);

    if (!$employee) {
        die("Employee not found.");
    }
} else {
    header("Location: view_employee.php");
    exit();
}

include "../includes/header.php";
?>

<div class="admin-container">
    <?php 
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'hr') {
        include "../includes/hr_sidebar.php";
    }
    ?>
    <div class="add-user">
        <h1>Edit Employee</h1>
        <form method="POST">
            <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required><br>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required><br>
            
            <label for="designation">Designation:</label>
            <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($employee['designation']); ?>" required><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required><br>

            <label for="salary">Salary:</label>
            <input type="number" step="0.01" id="salary" name="salary" value="<?php echo $employee['salary']; ?>" required><br>

            <label for="joining_date">Joining Date:</label>
            <input type="date" id="joining_date" name="joining_date" value="<?php echo $employee['joining_date']; ?>" required><br>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="active" <?php if($employee['status'] == 'active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($employee['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
            </select>
            <button type="submit">Update Employee</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
