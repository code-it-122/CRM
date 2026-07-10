<?php
 include "../includes/header.php";
 include "../database/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if(empty($name) || empty($email) || empty($phone) || empty($address)){
        echo "<script>alert('Please fill all the required fields');</script>";
    } 
 }

?>

<div class="admin-container">
   <?php
if($_SESSION['role'] == 'admin'){
    include "../includes/admin_sidebar.php";
}
elseif($_SESSION['role'] == 'sales'){
    include "../includes/sales_sidebar.php";
}
?>
    <div class="add-user">
        <h1>Add Customer</h1>
        <form action="view_customer.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="phone">Phone:</label>
            <input type="phone" id="phone" name="phone" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="address">Address:</label>
            <textarea name="address" id="address" col="30" roe="3"></textarea>

            <button type="submit">Add Customer</button>
        </form>
    </div>
