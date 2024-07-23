<?php
include 'db.php';
$username = $_POST["username"]; // Corrected case for POST variable
$email = $_POST["email"]; // Corrected case for POST variable
$password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password

$query_sql = "INSERT INTO users (Username, Email, Password) 
            VALUES ('$username', '$email', '$password')";

if (mysqli_query($conn, $query_sql)) {
    echo "<script>alert('Sign up successful'); window.location.href = 'login.html';</script>"; // Redirect with JavaScript after successful sign-up
} else {
    echo "Failed : " . mysqli_error($conn);
}
?>