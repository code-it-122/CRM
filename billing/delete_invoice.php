<?php
include "../database/db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM invoices WHERE invoice_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_invoice.php");
        exit();
    } else {
        die("Error deleting invoice: " . mysqli_stmt_error($stmt));
    }
} else {
    header("Location: view_invoice.php");
    exit();
}
?>