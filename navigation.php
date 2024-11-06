<?php

$staff_name = isset($_SESSION['s_fullname']) ? $_SESSION['s_fullname'] : 'Staff';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if the user is logged in
if (!isset($_SESSION['s_username'])) {
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
  <title>Staff</title>
  <link rel="stylesheet" href="css/nav.css">
</head>
<body>
  <header>
    <div class="logo"><img src="images/#.jpg" alt="Company Logo"></div>
    <nav>
      <a class="active" href="#.php">Home</a>
      <a href="#.php">Kutipan</a>
      <a href="#.php">Agih</a>
      <a href="#.php">Amil</a>
    </nav>
    <div class="dropdown">
      <div class="profile">
        <img src="images/profile.png" alt="Profile Icon"/>
        <button class="dropbtn"><?php echo htmlspecialchars($staff_name); ?></button>
      </div>
      <div class="dropdown-content">
        <a href="#.php">Profile</a>
        <a href="logout.php">Log Out</a>
      </div>
    </div>
  </header>
</body>
</html>
