<?php
session_start();
include("dbconn.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve username and password
    $username = mysqli_real_escape_string($dbconn, $_POST['un']);
    $password = mysqli_real_escape_string($dbconn, $_POST['password']);

    // Define a function to verify user credentials based on user role
    function verifyUser($dbconn, $username, $password, $table, $username_col, $password_col, $session_name, $name_col, $redirect_page) {
        $sql = "SELECT * FROM $table WHERE $username_col = ?";
        $stmt = $dbconn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: (" . $dbconn->errno . ") " . $dbconn->error);
        }

        // Bind and execute statement
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verify that a user was found and check the password
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Check if the provided password matches the stored hashed password
            if (password_verify($password, $user[$password_col])) {
                // Set session variables
                $_SESSION[$session_name] = $user[$username_col];
                $_SESSION[$session_name . '_name'] = $user[$name_col];
                header("Location: $redirect_page");
                exit();
            }
        }
    }

    // Check for admin user (assuming 'username' and 'password' are column names in the 'admin' table)
    verifyUser($dbconn, $username, $password, 'admin', 'a_username', 'a_pass', 'admin', 'a_fullname', 'adminpage.php');

    // Check for staff user (using 's_username' and 's_pass' for the 'staff' table)
    verifyUser($dbconn, $username, $password, 'staff', 's_username', 's_pass', 'staff', 's_fullname', 'mainpage.php');

    // After calling verifyUser() in loginSession.php
    if (isset($_SESSION['s_username']) || isset($_SESSION['a_username'])) {
        echo "Login successful";
    } else {
        echo "Login failed";
    }
    exit();


    // If login fails for both roles
    $_SESSION['error'] = "Invalid username or password.";
    header("Location: loginpage.php");
    exit();
}

// Close the database connection
mysqli_close($dbconn);
?>
