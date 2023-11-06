<?php
require_once '../../include/config.php'; // Include your database connection configuration

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Perform the deletion based on the course ID
    $delete_query = "DELETE FROM Matkul WHERE id = $id";

    if ($conn->query($delete_query) === TRUE) {
        // Redirect back to the course list with a success message
        header("Location: ../matakuliah.php.php?success=hapus");
        exit();
    } else {
        // Redirect back to the course list with an error message
        header("Location: ../matakuliah.php.php?error=hapus");
        exit();
    }
} else {
    // Redirect back to the course list if the course ID is not provided
    header("Location: ../matakuliah.php.php");
    exit();
}
