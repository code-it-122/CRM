<?php


include "../includes/header.php";
include "../database/db.php";
?>

<div class="admin-container">
    <?php 
    if ($_SESSION['role'] == 'admin') {
        include "../includes/admin_sidebar.php";
    } elseif ($_SESSION['role'] == 'hr') {
        include "../includes/hr_sidebar.php";
    }
    ?>
    <div class="add-user">
        <h1>Add Employee</h1>
        <form action="view_employee.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" required><br>
            
            <label for="designation">Designation:</label>
            <input type="text" id="designation" name="designation" required><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="salary">Salary:</label>
            <input type="number" step="0.01" id="salary" name="salary" required><br>

            <label for="joining_date">Joining Date:</label>
            <input type="date" id="joining_date" name="joining_date" required><br>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button type="submit">Add Employee</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
