<?php
include "../database/db.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        $sql = "DELETE FROM leads WHERE lead_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: view_leads.php");
            exit();
        } else {
            echo "<script>alert('Error deleting lead.'); window.location.href='view_leads.php';</script>";
            exit();
        }
    }
} else {
    echo "Error in deleting lead";
}
?>