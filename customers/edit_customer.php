<?php
 include "../includes/header.php";
 include "../database/db.php";

 if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id=$_GET['id'];   
     $sql = "SELECT * FROM customers WHERE customer_id=?";
     $stmt=mysqli_prepare($conn,$sql);
     mysqli_stmt_bind_param($stmt,"i",$id);
     mysqli_stmt_execute($stmt);
     $result=mysqli_stmt_get_result($stmt);
     $customer=mysqli_fetch_assoc($result);

    $name = $customer['name'];
    $phone = $customer['phone'];
    $email = $customer['email'];
    $address = $customer['address']; 
 }


?>

<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Edit Customer</h1>
        <form action="edit_customer.php" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $name ?>" required><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo $phone ?>" required><br>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo $email ?>" required><br>

            <label for="address">Address:</label>
            <textarea cols='30' rows='3' id="address" name="address" value="<?php echo $address ?>"></textarea><br>

            <button type="submit">Edit Customer</button>
        </form>
    </div>
    <?php
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
    mysqli_stmt_bind_param($stmt, "ssssi",
       $name,
       $phone,
       $email,
       $address,
       $customer_id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_customer.php");
        exit();
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
}
    ?>
