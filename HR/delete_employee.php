<?php

include "../database/db.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'hr'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        $sql = "DELETE FROM employees WHERE employee_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Employee deleted successfully');
                    window.location.href = 'view_employee.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('This employee cannot be deleted because they have related attendance or leave records.'); window.location.href='view_employee.php';</script>";
            exit();
        }
    }
} else {
    header("Location: view_employee.php");
    exit();
}
?>