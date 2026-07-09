<?php
 include "../includes/header.php";
 include "../database/db.php";

 if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id=$_GET['id'];   
     $sql = "SELECT * FROM employees WHERE employee_id=?";
     $stmt=mysqli_prepare($conn,$sql);
     mysqli_stmt_bind_param($stmt,"i",$id);
     mysqli_stmt_execute($stmt);
     $result=mysqli_stmt_get_result($stmt);
     $employee=mysqli_fetch_assoc($result);

    $name = $employee['name'];
    $department = $employee['department'];
    $designation = $employee['designation'];
    $phone = $employee['phone'];
    $email = $employee['email'];
    $salary = $employee['salary'];
    $joining_date = $employee['joining_date'];
    $status = $employee['status'];
 }


?>
    <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $employee_id = $_POST['employee_id'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $designation = $_POST['designation'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $salary = $_POST['salary'];
    $joining_date = $_POST['joining_date'];
    $status = $_POST['status'];

    $sql = "UPDATE employees
            SET name = ?, department = ?, designation = ?, phone = ? ,email = ?, salary = ? ,joining_date = ?, status = ? 
            WHERE employee_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssdssi",
       $name,
     $department ,
    $designation ,
    $phone ,
    $email,
    $salary,
    $joining_date,
    $status,
    $employee_id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: view_employee.php");
        exit();
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
}
    ?>
<div class="admin-container">
    <?php  include "../includes/admin_sidebar.php"; ?>
    <div class="add-user">
        <h1>Edit Employee</h1>
        <form method="POST">
            <input type="hidden" name="employee_id" value="<?php echo $employee['employee_id']; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $employee['name']; ?>" required><br>

            <label for="department">department:</label>
            <input type="text" id="department" name="department" value="<?php echo $employee['department']; ?>" required><br>
            
            <label for="designation">designation:</label>
            <input type="text" id="designation" name="designation" value="<?php echo $employee['designation']; ?>" required><br>

            <label for="phone">Phone:</label>
            <input type="phone" id="phone" name="phone" value="<?php echo $employee['phone']; ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $employee['email']; ?>" required><br>

            <label for="salary">salary:</label>
           <input type="text" id="salary" name="salary" value="<?php echo $employee['salary']; ?>" required><br>

           <label for="joining_date">Joining Date :</label>
            <input type="date" id="joining_date" name="joining_date" value="<?php echo $employee['joining_date']; ?>"><br>
             <label for="status">Status:</label>
             <select id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="active" <?php if($employee['status'] == 'active') echo 'selected'; ?>>active</option>
                <option value="inactive" <?php if($employee['status'] == 'inactive') echo 'selected'; ?>>inactive</option>
</select>
            <button type="submit">Edit Employee</button>
        </form>
    </div>
</div>

