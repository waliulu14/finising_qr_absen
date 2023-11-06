<?php
$host = "154.41.240.1"; 
$username = "u729021907_diana"; 
$password = "Huliselan14@"; 
$database = "u729021907_diana"; 

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
} 
?>


<!-- 
 $host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "qr-absensi"; 

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}  -->