<?php
$host     = "localhost";
$user     = "root";
$password = "";          // default XAMPP password is empty
$database = "student_management_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("<div style='font-family:monospace;color:red;padding:20px;'>
        ⚠️ Database connection failed: " . mysqli_connect_error() . "<br>
        Make sure XAMPP is running and you have imported the SQL file.
    </div>");
}
?>
