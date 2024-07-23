<?php
include 'db.php';
$username = $_POST["username"];
$password = $_POST["password"];

$query_sql = "SELECT * FROM users 
            WHERE username = '$username'";

$result = mysqli_query($conn, $query_sql);

if (mysqli_num_rows($result) > 0) {
    // User found, verify password
    $row = mysqli_fetch_assoc($result);
    $hashed_password = $row['password'];
    if (password_verify($password, $hashed_password)) {
        // Password matches, start session and redirect
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $row['id'];
        header("Location: index.php");
        exit();
    } else {
        // Password does not match
        echo "<script>alert('Username or password is incorrect. Please try again.'); window.location.href = 'login.html';</script>";
    }
} else {
    // User not found
    echo "<script>alert('Username or password is incorrect. Please try again.'); window.location.href = 'login.html';</script>";
}
?>
