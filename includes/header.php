<?php
session_start();
?>

<html>
    <head><title>CRM System</title>
    <link rel="stylesheet"  href="../assests/css/style.css">
</head>
    <body>
       <header class="header">

    <div class="logo">
        <h2>CRM SYSTEM</h2>
    </div>
    <div class="user-info">
        <h3>Welcome, <?php echo $_SESSION['name'] ?? "";?></h3>
        <p>Role: <?php echo ($_SESSION['role']) ?? "";?></p>
    </div>


</header>