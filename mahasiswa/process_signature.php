<?php
// Include configuration file and connect to the database
require_once '../include/config.php';

// Start the session
session_start();

// Function to sanitize input data
function sanitizeInput($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $matkul_id = sanitizeInput($_POST['matkul_id']);

    // Check if id_mahasiswa is set in the session
    if(isset($_SESSION['id_mahasiswa'])) {
        $id_mahasiswa = $_SESSION['id_mahasiswa'];
        
        // Assuming you have a session variable for student ID
        $signature_data = $_POST['signature'];

        // Insert signature into the tanda_tangan_mahasiswa table
        $query = "INSERT INTO tanda_tangan_mahasiswa (id_mahasiswa, id_matkul, tgl_tanda_tangan, tanda_tangan)
                  VALUES ($id_mahasiswa, $matkul_id, NOW(), '$signature_data')";
        
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Signature successfully stored
            echo "Signature successfully stored.";
        } else {
            // Error storing signature
            echo "Error storing signature.";
        }
    } else {
        // If id_mahasiswa is not set in the session
        echo "User ID not found in session.";
    }
} else {
    // If the form is not submitted via POST method
    echo "Invalid request.";
}
?>
