<?php

include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $sql = "INSERT INTO employees (name, department, designation, phone, email, salary, joining_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssdss", $name, $department, $designation, $phone, $email, $salary, $joining_date, $status);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>
                alert('Employee added successfully');
                window.location.href = 'view_employee.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }
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
    <div class="view">
        <h1>Employees</h1>
        <hr>
        <a href="add_employee.php" class="add-btn">Add Employee</a>
        <table class="table-container">
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Salary</th>
                <th>Joining Date</th>
                <th>Status</th>
                <th colspan="2">Actions</th>
            </tr>
            <?php
            $sql = "SELECT * FROM employees ORDER BY employee_id DESC";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                while ($employee = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $employee['employee_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($employee['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($employee['department']) . "</td>";
                    echo "<td>" . htmlspecialchars($employee['designation']) . "</td>";
                    echo "<td>" . htmlspecialchars($employee['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($employee['email']) . "</td>";
                    echo "<td>$" . number_format($employee['salary'], 2) . "</td>";
                    echo "<td>" . $employee['joining_date'] . "</td>";
                    echo "<td>" . ucfirst($employee['status']) . "</td>";
                    echo "<td class='edit-btn'><a href='edit_employee.php?id=" . $employee['employee_id'] . "'>Edit</a></td>";
                    echo "<td class='delete-btn'>
                            <a href='delete_employee.php?id=" . $employee['employee_id'] . "'
                               onclick=\"return confirm('Are you sure you want to delete this employee?');\">
                               Delete
                            </a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No employees found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>