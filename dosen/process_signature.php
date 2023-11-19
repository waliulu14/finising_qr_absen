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

    // Insert the signature into the tanda_tangan_dosen table
    $insertSignatureQuery = "INSERT INTO tanda_tangan_dosen (id_dosen, id_matkul, tgl_tanda_tangan, tanda_tangan) VALUES (1, ?, CURDATE(), ?)";
    $stmt = $conn->prepare($insertSignatureQuery);
    $stmt->bind_param('is', $matkul_id, $decodedSignature);
    $stmt->execute();
    $stmt->close();

    // Query to get course information and count of students present
    $attendanceQuery = "SELECT M.nama_matkul, COUNT(DISTINCT AM.id_mahasiswa) AS jumlah_absensi
                        FROM matkul M
                        LEFT JOIN absensi_mahasiswa AM ON M.id = AM.id_matkul
                        WHERE M.id = ? AND AM.tanggal = CURDATE() AND AM.hadir = TRUE";
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
           

            <h2>Students Present Today</h2>
            <table class='table'>
                
                <tbody>";

    // Query to get the names and attendance status of students from history_absensi_mahasiswa
    $queryStudents = "SELECT M.nama_mahasiswa, MAX(HAM.tanggal) AS last_attendance_date, COUNT(HAM.id_mahasiswa) AS attendance_count
FROM mahasiswa M
LEFT JOIN history_absensi_mahasiswa HAM ON M.id = HAM.id_mahasiswa AND HAM.tanggal = CURDATE()
WHERE HAM.id_matkul = ?
GROUP BY M.nama_mahasiswa";

    $stmtStudents = $conn->prepare($queryStudents);
    $stmtStudents->bind_param('i', $matkul_id);
    $stmtStudents->execute();
    $stmtStudents->bind_result($nama_mahasiswa, $last_attendance_date, $attendance_count);

    echo "
<tr>
<th>Name</th>
<th>Status</th>
<th>Attendance Count</th>
</tr>";

    while ($stmtStudents->fetch()) {
        // Determine attendance status
        $status = ($last_attendance_date !== null) ? 'Present' : 'Absent';

        // Display student name, status, and attendance count in the table
        echo "<tr>
<td>$nama_mahasiswa</td>
<td>$status</td>
<td>$attendance_count</td>
</tr>";
    }

    $stmtStudents->close();




    // Complete the HTML structure
    echo "
                </tbody>
            </table>
            <p>The digital signature has been stored in the database.</p>
        </div>
    </body>
    </html>";

    include 'assets/footer.php'; // Include footer.php
} else {
    echo "Invalid Request.";
}
