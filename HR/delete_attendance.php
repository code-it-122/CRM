<?php


include "../database/db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        $sql = "DELETE FROM attendance WHERE attendance_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Attendance record deleted successfully');
                    window.location.href = 'view_attendance.php';
                  </script>";
            exit();
        } else {
            die("Error deleting attendance: " . mysqli_stmt_error($stmt));
        }
    }
} else {
    header("Location: view_attendance.php");
    exit();
}
?>
