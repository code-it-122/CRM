<?php
include "../database/db.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM invoices WHERE invoice_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_invoice.php");
        exit();
    } else {
        echo "<script>alert('Error deleting invoice.'); window.location.href='view_invoice.php';</script>";
        exit();
    }
} else {
    header("Location: view_invoice.php");
    exit();
}
?>