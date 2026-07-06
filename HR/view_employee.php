<?php
 include "../includes/header.php";
 include "../includes/footer.php";
 include "../database/db.php";
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
      </tr>";
      while( $Employee=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$Employee['employee_id']."</td><td>".
       $Employee['employee_name']."</td><td>".
       $Employee['department']."</td><td>".
       $Employee['designation']."</td><td>".
       $Employee['phone']."</td><td>".
       $Employee['email']."</td><td>".
       $Employee['salary']."</td><td>".
       $Employee['joining_date']."</td><td>".
       $Employee['status']."</td>";
          echo "</tr>";
      }

 echo "</table>";
 echo "</div>";
 echo "</div>";
?>