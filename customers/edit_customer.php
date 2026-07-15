<?php
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $sql = "SELECT * FROM customers WHERE customer_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $customer = mysqli_fetch_assoc($result);

    $name = $customer['name'];
    $phone = $customer['phone'];
    $email = $customer['email'];
    $address = $customer['address'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE customers
            SET name = ?, phone = ?, email = ?, address = ?
            WHERE customer_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $name, $phone, $email, $address, $customer_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_customer.php");
        exit();
    } else {
        echo "<script>alert('Error updating customer');</script>";
    }
}
include "../includes/header.php";
?>

<div class="admin-container">
    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-user-pen';
        $ph_title = 'Edit Customer';
        $ph_subtitle = 'Update this customer\'s details.';
        $ph_back_link = 'view_customer.php';
        $ph_back_label = 'Back to Customers';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="edit_customer.php" method="POST">
                            <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-id-card me-1"></i> Basic Information
                            </h6>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-dark">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-address-book me-1"></i> Contact Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold text-dark">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                    <input type="text" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label fw-semibold text-dark">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Save Changes
                                </button>
                                <a href="view_customer.php" class="btn btn-outline-secondary py-2 px-4">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>