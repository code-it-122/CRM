<?php
 include "../includes/header.php";
 include "../includes/footer.php";
 include "../database/db.php";
 echo "<div class='admin-container'>";
 include "../includes/admin_sidebar.php";
 echo "<div class=\"view\">";
 $sql="SELECT * FROM users";
 $result=mysqli_query($conn,$sql);

 
 echo "<h1>Users</h1><hr>";
 echo "<a href='add_user.php' class='add-btn'>Add User</a>";
 echo "<table class=\"table-container\">";
echo "<tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th colspan='2'>Actions</th>
      </tr>";
      while( $user=mysqli_fetch_assoc($result)){
 echo "<tr><td>".$user['user_id']."</td><td>".
       $user['name']."</td><td>".
       $user['email']."</td><td>".
       $user['role']."</td><td>".
       $user['status']."</td>";
 echo "<td class=\"edit-btn\"><a href='edit_user.php?id=".$user['user_id']."'>Edit</a></td>";
 echo "<td class=\"delete-btn\"><a href='delete_user.php?id=".$user['user_id']."'>Delete</a></td></tr>";
      }

 echo "</table>";
 echo "</div>";
 echo "</div>";
?>