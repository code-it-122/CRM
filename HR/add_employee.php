<?php
 include "../includes/header.php";
 include "../database/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
     $depatment = $_POST['department'];
    $designation = $_POST['designation'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $salary = $_POST['salary'];
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];

    if(empty($name)|| empty($department) || empty($designation)|| empty($email) || empty($phone) || empty($joining_date)){
        echo "<script>alert('Please fill all the required fields');</script>";
    } 
 }

?>

<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Add Employee</h1>
        <form action="view_employee.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="department">department:</label>
            <input type="text" id="department" name="department" required><br>
            
            <label for="designation">designation:</label>
            <input type="text" id="designation" name="designation" required><br>

            <label for="phone">Phone:</label>
            <input type="phone" id="phone" name="phone" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="salary">salary:</label>
           <input type="text" id="salary" name="salary" required><br>

           <label for="joining_date">Joining Date :</label>
            <input type="date" id="joining_date" name="joining_date"><br>
             <label for="status">Status:</label>
             <select id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="active">active</option>
                <option value="inactive">inactive</option>
</select>
            <button type="submit">Add Employee</button>
        </form>
    </div>
</div>
