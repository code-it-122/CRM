<?php
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE product_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    $product_name = $product['product_name'];
    $category = $product['category'];
    $price = $product['price'];
    $stock = $product['stock'];
    $description = $product['description'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $sql = "UPDATE products
            SET product_name = ?, category = ?, price = ?, stock = ? ,description=?
            WHERE product_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdisi",
        $product_name,
        $category,
        $price,
        $stock,
        $description,
        $product_id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_product.php");
        exit();
    } else {
        echo "<script>alert('Error updating product');</script>";
    }
}
include "../includes/header.php";
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-pen-to-square';
        $ph_title = 'Edit Product';
        $ph_subtitle = 'Update product details and inventory.';
        $ph_back_link = 'view_product.php';
        $ph_back_label = 'Back to Products';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-tag me-1"></i> Product Details
                            </h6>
                            <div class="mb-3">
                                <label for="product_name" class="form-label fw-semibold text-dark">Product Name</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product_name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label fw-semibold text-dark">Category</label>
                                <input type="text" id="category" name="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold text-dark">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-coins me-1"></i> Pricing &amp; Inventory
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="price" class="form-label fw-semibold text-dark">Price (Rs)</label>
                                    <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="stock" class="form-label fw-semibold text-dark">Stock Quantity</label>
                                    <input type="number" min="0" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Save Changes
                                </button>
                                <a href="view_product.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>