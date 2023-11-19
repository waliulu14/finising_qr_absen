<?php
require_once '../../include/config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if 'id' parameter is present in the URL
    if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
        $id = $_GET["id"];

        // Prepare a DELETE statement to remove the course from the 'matkul' table
        $deleteQuery = "DELETE FROM matkul WHERE id = ?";

        if ($stmt = $conn->prepare($deleteQuery)) {
            $stmt->bind_param("i", $id);

            // Execute the DELETE statement
            if ($stmt->execute()) {
                // Course deletion was successful
                header("Location: ../matakuliah.php?success=hapus");
                exit();
            } else {
                // Course deletion failed
                header("Location: ../matakuliah.php?error=hapus");
                exit();
            }

            $stmt->close();
        }
    } else {
        // 'id' parameter is not valid
        header("Location: ../matakuliah.php?error=invalid_id");
        exit();
    }
} else {
    // Invalid request method
    header("Location: ../matakuliah.php");
    exit();
}

$conn->close();
?>
