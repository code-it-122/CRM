<?php
 include "../includes/header.php";
 include "../database/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    if(empty($product_name) || empty($category) || empty($price) || empty($stock) || empty($description)){
        echo "<script>alert('Please fill all the required fields');</script>";
    } 
 }

?>

<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Add Product</h1>
        <form action="view_product.php" method="POST">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required><br>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required><br>

            <label for="price">Price:</label>
            <input type="price" id="price" name="price" required><br>

            <label for="stock">Stock:</label>
            <input type="stock" id="stock" name="stock" required><br>

            <label for="description">Description:</label>
            <textarea cols='30' rows='3' id="description" name="description"></textarea><br>

            <button type="submit">Add Product</button>
        </form>
    </div>
