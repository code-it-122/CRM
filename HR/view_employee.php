<?php
 include "../includes/header.php";
 include "../includes/footer.php";
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


    $sql="insert into employees (name,department,designation,phone,email,salary,joining_date,status) values(?,?,?,?,?,?,?,?)";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, "sssssdss", $name, $depatment, $designation,$phone, $email, $salary,$joining_date,$status);
    $result=mysqli_stmt_execute($stmt);

    if($result){
        echo "<script>alert('Employee added successfully');</script>";
        header("Location: view_employee.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt));
    }

 }


 echo "<div class='admin-container'>";
 include "../includes/admin_sidebar.php";
 echo "<div class=\"view\">";
 $sql="SELECT * FROM employees
 ";
 $result=mysqli_query($conn,$sql);

 
 echo "<h1>Employees</h1><hr>";
 echo "<a href='add_employee.php' class='add-btn'>Add Employee</a>";
 echo "<table class=\"table-container\">";
echo "<tr>
        <th>Employee ID</th>
        <th>Employee Name</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Salary</th>
        <th>Joining Date</th>
        <th>Status</th>
        <th colspan='2'>Actions</th>
      </tr>";
      while( $Employee=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$Employee['employee_id']."</td><td>".
       $Employee['name']."</td><td>".
       $Employee['department']."</td><td>".
       $Employee['designation']."</td><td>".
       $Employee['phone']."</td><td>".
       $Employee['email']."</td><td>".
       $Employee['salary']."</td><td>".
       $Employee['joining_date']."</td><td>".
       $Employee['status']."</td>";
        echo "<td class=\"edit-btn\"><a href='edit_employee.php?id=".$Employee['employee_id']."'>Edit</a></td>";
 echo "<td class=\"delete-btn\">
        <a href='delete_employee.php?id=".$Employee['employee_id']."'
           onclick=\"return confirm('Are you sure you want to delete this user?');\">
           Delete
        </a>
      </td></tr>";
          echo "</tr>";
      }
       
 echo "</table>";
 echo "</div>";
 echo "</div>";
?>