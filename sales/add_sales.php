<?php
include "../includes/header.php";
include "../database/db.php";

$sql = "SELECT * FROM customers";
$result = mysqli_query($conn, $sql);

$sql = "SELECT * FROM products";
$result1 = mysqli_query($conn, $sql);
?>

<div class="admin-container">
    <?php if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
} ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-money-bill-trend-up';
        $ph_title = 'Add Sale';
        $ph_subtitle = 'Record a new sale.';
        $ph_back_link = 'view_sales.php';
        $ph_back_label = 'Back to Sales';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="view_sales.php" method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-user me-1"></i> Customer &amp; Product
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Customer</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Select Customer</option>
                                    <?php
                                    while($cust = mysqli_fetch_assoc($result)){
                                        echo "<option value='".$cust['customer_id']."'>".$cust['name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    <?php
                                    while($prod = mysqli_fetch_assoc($result1)){
                                        echo "<option value='".$prod['product_id']."'>".$prod['product_name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Sale Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-dark">Quantity</label>
                                    <input type="number" name="quantity" class="form-control" min="1" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-dark">Sale Date</label>
                                    <input type="date" name="sale_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Add Sale
                                </button>
                                <a href="view_sales.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>