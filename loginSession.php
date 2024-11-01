<?php
session_start();
include("dbconn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($dbconn, $_POST['un']);
    $password = mysqli_real_escape_string($dbconn, $_POST['password']);

    function verifyUser($dbconn, $username, $password, $table, $username_col, $password_col, $session_name, $name_col, $redirect_page) {
        $sql = "SELECT * FROM $table WHERE $username_col = ? AND $password_col = ?";
        $stmt = $dbconn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: (" . $dbconn->errno . ") " . $dbconn->error);
        }

        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Set session variables
            $_SESSION[$session_name] = $user[$username_col];
            $_SESSION[$session_name . '_name'] = $user[$name_col];
            header("Location: $redirect_page");
            exit();
        }
    }

    // Check for admin user (assuming 'username' and 'password' are column names in the 'admin' table)
    verifyUser($dbconn, $username, $password, 'admin', 'a_username', 'a_pass', 'a_username', 'a_fullname', 'adminpage.php');

    // Check for staff user (using 's_username' and 's_pass' for the 'staff' table)
    verifyUser($dbconn, $username, $password, 'staff', 's_username', 's_pass', 's_username', 's_fullname', 'mainpage.php');

    // If login fails for both roles
    $_SESSION['error'] = "Invalid username or password.";
    header("Location: loginpage.php");
    exit();
}

mysqli_close($dbconn);
?>
