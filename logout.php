<?php
session_start();
session_destroy(); // Hentikan sesi
header('Location: index.php'); // Arahkan kembali ke halaman login atau halaman utama
exit();
?>
