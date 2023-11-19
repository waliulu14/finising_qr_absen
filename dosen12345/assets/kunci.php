<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: ../login.php');
    exit(); // Stop script execution
}

include_once '../include/config.php';

$username = $_SESSION['username'];

// Ambil informasi pengguna dari database
$query = "SELECT * FROM user WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $namaPengguna = $row['username'];
}

// If the user is logged in, you can continue with the page's content
?>

