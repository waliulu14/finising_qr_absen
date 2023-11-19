<?php
session_start();

// Include the database connection
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pass']; // Adjust this based on your actual form field name

    // Query to get user information including level
    $query = "SELECT id, level, username, password FROM user WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Check if the password is correct
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_level'] = $row['level'];
            $_SESSION['username'] = $row['username'];

            // Redirect based on user level
            if ($_SESSION['user_level'] == 'admin') {
                header('Location: ../admin/dashboard.php');
            } elseif ($_SESSION['user_level'] == 'dosen') {
                header('Location: ../dosen/index.php');
            } elseif ($_SESSION['user_level'] == 'mahasiswa') {
                // Redirect to the student dashboard and store student ID in session
                header('Location: ../mahasiswa/index.php');
                $_SESSION['mahasiswa_id'] = getMahasiswaId($username, $conn);
            }
        } else {
            // Incorrect password, provide an error message
            $error = "Username or password is incorrect";
            header('Location: ../index.php?error=' . $error);
        }
    } else {
        // Username not found, provide an error message
        $error = "Username not found";
        header('Location: ../index.php?error=' . $error);
    }
}

// Close the database connection
$conn->close();

function getMahasiswaId($username, $conn) {
    // Query to get mahasiswa_id based on username
    $query = "SELECT id FROM mahasiswa WHERE id_user = (SELECT id FROM user WHERE username = '$username')";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    } else {
        // Return an appropriate value or handle the case where data is not found
        return null;
    }
}
?>
