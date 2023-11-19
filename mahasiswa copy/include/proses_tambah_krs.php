<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection
    require_once '../../include/config.php';

    // Get the selected Mahasiswa and Matkul IDs from the form
    $mahasiswaId = $_POST['mahasiswa'];
    $matkulId = $_POST['matkul'];

    // Perform the SQL insert to add the KRS entry
    $sql = "INSERT INTO krs (id_mahasiswa, id_matkul) VALUES (?, ?)";

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the parameters
        $stmt->bind_param("ii", $mahasiswaId, $matkulId);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // KRS entry added successfully
            header("Location: ../krs.php?success=tambah");
        } else {
            // Error while executing the statement
            header("Location: ../krs.php?error=tambah");
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Error while preparing the statement
        header("Location: ../krs.php?error=tambah");
    }

    // Close the database connection
    $conn->close();
} else {
    // Redirect to the KRS page if the form was not submitted
    header("Location: ../krs.php");
}
?>
