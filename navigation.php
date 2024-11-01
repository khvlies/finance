<?php
session_start();
$staff_name = isset($_SESSION['s_fullname']) ? $_SESSION['s_fullname'] : 'Staff';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Check if the user is logged in
if (!isset($_SESSION['staff'])) {
    header("Location: loginpage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="#.png"/>
  <link rel="icon" href="#.png"/>
  <title>Staff</title>
  <link rel="stylesheet" href="css/nav.css">
</head>
<body>
  <header>
    <div class="logo"><img src="images/selfo.jpg" alt="Company Logo"></div>
    <nav>
      <a class="active" href="#.php">Home</a>
      <a href="#.php">AMIL</a>
      <a href="#.php">KUTIPAN</a>
      <a href="#.php">AGIH</a>
    </nav>
    <div class="dropdown">
      <div class="profile">
        <img src="images/#.png" alt="Profile Icon"/>
        <button class="dropbtn"><?php echo htmlspecialchars($admin_name); ?></button>
      </div>
      <div class="dropdown-content">
        <a href="#.php">Profile</a>
        <a href="logout.php">Log Out</a>
      </div>
    </div>
  </header>
</body>
</html>