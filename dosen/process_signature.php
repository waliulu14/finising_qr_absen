<?php
// Include configuration file and connect to the database
require_once '../include/config.php';
require_once 'assets/navbar.php';

// Function to sanitize input data
function sanitizeInput($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $matkul_id = sanitizeInput($_POST['matkul_id']);
    $signatureData = sanitizeInput($_POST['signature']);

    // Decode the data URL to get the actual signature image
    $decodedSignature = base64_decode(str_replace('data:image/png;base64,', '', $signatureData));

    // Insert the signature into the tanda_tangan_mahasiswa table
    $insertSignatureQuery = "INSERT INTO tanda_tangan_mahasiswa (id_mahasiswa, id_matkul, tgl_tanda_tangan, tanda_tangan) VALUES (?, ?, CURDATE(), ?)";
    $stmt = $conn->prepare($insertSignatureQuery);

    // Assuming you have a session variable for student ID
    $id_mahasiswa = $_SESSION['id_mahasiswa'];
    $stmt->bind_param('iis', $id_mahasiswa, $matkul_id, $decodedSignature);

    $stmt->execute();
    $stmt->close();

    // Query to get course information and count of students present
    $attendanceQuery = "SELECT M.nama_matkul, COUNT(DISTINCT TTM.id_mahasiswa) AS jumlah_absensi
                        FROM matkul M
                        LEFT JOIN tanda_tangan_mahasiswa TTM ON M.id = TTM.id_matkul
                        WHERE M.id = ? AND TTM.tgl_tanda_tangan = CURDATE()";
    $stmtAttendance = $conn->prepare($attendanceQuery);
    $stmtAttendance->bind_param('i', $matkul_id);
    $stmtAttendance->execute();
    $stmtAttendance->bind_result($nama_matkul, $jumlah_absensi);
    $stmtAttendance->fetch();
    $stmtAttendance->close();

    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Signature Processed</title>
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
    </head>
    <body>
        <div class='container mt-5'>
            <h1>Signature Processed Successfully</h1>
            <p>Course: $nama_matkul</p>
           
            <h2>Your Attendance Today</h2>
            <table class='table'>
                
                <tbody>";

    // Query to get the attendance status of the current student
    $queryStudentAttendance = "SELECT COUNT(id) AS attendance_count FROM tanda_tangan_mahasiswa WHERE id_mahasiswa = ? AND id_matkul = ? AND tgl_tanda_tangan = CURDATE()";
    $stmtStudentAttendance = $conn->prepare($queryStudentAttendance);
    $stmtStudentAttendance->bind_param('ii', $id_mahasiswa, $matkul_id);
    $stmtStudentAttendance->execute();
    $stmtStudentAttendance->bind_result($attendance_count);
    $stmtStudentAttendance->fetch();
    $stmtStudentAttendance->close();

    // Determine attendance status
    $status = ($attendance_count > 0) ? 'Present' : 'Absent';

    // Display student name, status, and attendance count in the table
    echo "<tr>
<td>Your Name</td>
<td>$status</td>
<td>$attendance_count</td>
</tr>";

    // Complete the HTML structure
    echo "
                </tbody>
            </table>
            <p>Your digital signature has been stored in the database.</p>
        </div>
    </body>
    </html>";

    include 'assets/footer.php'; // Include footer.php
} else {
    echo "Invalid Request.";
}
?>
