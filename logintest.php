<?php
session_start();
include("dbconn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($dbconn, $_POST['un']);
    $password = mysqli_real_escape_string($dbconn, $_POST['password']);

    function verifyUser($dbconn, $name, $password, $table, $name_field, $password_field, $fullname_field, $session_name, $redirect_page) {
        $sql = "SELECT * FROM $table WHERE $name_field = ? AND $password_field = ?";
        $stmt = $dbconn->prepare($sql);
        $stmt->bind_param('ss', $name, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION[$session_name] = $user[$name_field];
            $_SESSION[$session_name.'_name'] = $user[$fullname_field];
            header("Location: $redirect_page");
            exit();
        }
    }

    verifyUser($dbconn, $name, $password, 'admin', 'a_username', 'a_pass', 'a_fullname', 'admin', 'adminpage.php');
    verifyUser($dbconn, $name, $password, 'staff', 's_username', 's_pass', 's_fullname', 'staff', 'mainpage.php');
    
    if (isset($_SESSION['s_username']) || isset($_SESSION['a_username'])) {
        echo "Login successful";
    } else {
        echo "Login failed";
    }
    exit();

    $_SESSION['error'] = "Invalid username or password.";
    header("Location: loginpage.php");
    exit();
}

mysqli_close($dbconn);
?>
