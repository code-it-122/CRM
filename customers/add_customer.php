<?php
include "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        echo "<script>alert('Please fill all the required fields');</script>";
    }
}
include "../includes/header.php";
?>

<div class="admin-container">
    <?php
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'sales') {
        include "../includes/sales_sidebar.php";
    }
    ?>

    <div class="view py-4 px-4">
        <?php
        $ph_icon = 'fa-user-plus';
        $ph_title = 'Add Customer';
        $ph_subtitle = 'Create a new customer record.';
        $ph_back_link = 'view_customer.php';
        $ph_back_label = 'Back to Customers';
        include "../includes/page_header.php";
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-md-5">
                        <form action="view_customer.php" method="POST">

                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-id-card me-1"></i> Basic Information
                            </h6>
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-dark">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. John Smith" required>
                            </div>

                            <h6 class="text-uppercase text-muted fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-address-book me-1"></i> Contact Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold text-dark">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. 9876543210" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold text-dark">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label fw-semibold text-dark">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3" placeholder="Street, city, state"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success flex-fill py-2 fw-semibold">
                                    <i class="fa-solid fa-circle-check me-2"></i>Add Customer
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