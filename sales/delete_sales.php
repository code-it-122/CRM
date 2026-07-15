<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../database/db.php";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id > 0) {
        mysqli_begin_transaction($conn);
        $ok = true;

        try {
            // Restore stock for every item in this sale before it's removed
            $sql = "SELECT product_id, quantity FROM sale_items WHERE sale_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $items = mysqli_stmt_get_result($stmt);

            while ($item = mysqli_fetch_assoc($items)) {
                $sql2 = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "ii", $item['quantity'], $item['product_id']);
                mysqli_stmt_execute($stmt2);
            }

            // Deleting the sale cascades to sale_items and invoices (FK ON DELETE CASCADE)
            $sql = "DELETE FROM sales WHERE sale_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
        } catch (Exception $e) {
            $ok = false;
        }

        if ($ok) {
            mysqli_commit($conn);
        } else {
            mysqli_rollback($conn);
        }

        header("Location: view_sales.php");
        exit();
    }
} else {
    echo "Error in deleting sales";
}
?>