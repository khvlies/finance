<?php
session_start();
$display_name = isset($_SESSION['staff_name']) ? $_SESSION['staff_name'] : 'User';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page if not logged in
if (!isset($_SESSION['staff'])) {
  header("Location: loginpage.php");
  exit();
}
?>
<link rel="stylesheet" href="../css/nav.css">
<header>
<div class="logo"><img src="../images/logo.png" alt="Company Logo"></div>
  <nav>
    <a href="../mainpage.php">Home</a>
    <a href="../kutipan/kutipanMain.php">Kutipan</a>
    <a href="../agihan/agihanMain.php">Agihan</a>
    <a href="#.php">Amil</a>
  </nav>
  <div class="dropdown">
        <div class="profile">
            <img src="../images/profile.png" alt="Profile Icon"/>
            <button class="dropbtn"><?php echo htmlspecialchars($display_name); ?></button>
        </div>
        <div class="dropdown-content">
            <a href="#.php">Profile</a>
            <a href="../logout.php">Log Out</a>
        </div>
    </div>
</header>