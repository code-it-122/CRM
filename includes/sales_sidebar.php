
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin_sidebar">
    <a href="../sales/sales_dashboard.php" class="<?php echo ($current_page == 'sales_dashboard.php') ? 'active' : ''; ?>">Dashboard</a><br>
    <a href="../customers/view_customer.php" class="<?php echo ($current_page == 'view_customer.php' || $current_page == 'add_customer.php' || $current_page == 'edit_customer.php') ? 'active' : ''; ?>">Customers</a><br>
    <a href="../products/view_product.php" class="<?php echo ($current_page == 'view_product.php' || $current_page == 'add_product.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>">Products</a><br>
    <a href="../sales/view_sales.php" class="<?php echo ($current_page == 'view_sales.php' || $current_page == 'add_sales.php' || $current_page == 'edit_sales.php') ? 'active' : ''; ?>">Sales</a><br>
    <a href="../billing/view_invoice.php" class="<?php echo ($current_page == 'view_invoice.php' || $current_page == 'add_invoice.php' || $current_page == 'edit_invoice.php') ? 'active' : ''; ?>">Billing</a><br>
    <br><br>
    <form method="POST" action="../auth/logout.php">
        <button type="submit" class="logout">Logout</button>
    </form>
</div>