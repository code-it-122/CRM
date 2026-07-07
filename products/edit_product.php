<?php
 include "../includes/header.php";
 include "../database/db.php";

 if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id=$_GET['id'];   
     $sql = "SELECT * FROM products WHERE product_id=?";
     $stmt=mysqli_prepare($conn,$sql);
     mysqli_stmt_bind_param($stmt,"i",$id);
     mysqli_stmt_execute($stmt);
     $result=mysqli_stmt_get_result($stmt);
     $product=mysqli_fetch_assoc($result);

    $product_name = $product['product_name'];
    $category = $product['category'];
    $price = $product['price'];
    $stock = $product['stock'];
    $description = $product['description'];     
 }


?>

<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Edit Product</h1>
        <form action="edit_product.php" method="POST">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" value="<?php echo $product_name ?>" required><br>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?php echo $category ?>" required><br>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo $price ?>" required><br>

            <label for="stock">Stock:</label>
            <input type="text" id="stock" name="stock" value="<?php echo $stock ?>" required><br>

            <label for="description">Description:</label>
            <textarea cols='30' rows='3' id="description" name="description" value="<?php echo $description ?>"></textarea><br>

            <button type="submit">Edit Product</button>
        </form>
    </div>
    <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $product_id= $_POST['product_id'];
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
        echo "Error: " . mysqli_stmt_error($stmt);
    }
}
    ?>
