<?php
session_start();
$staff_name = isset($_SESSION['s_username']) ? $_SESSION['s_username'] : 'Staff';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['staff'])) {
  header("Location: loginpage.php");
  exit();
}
?>
<link rel="stylesheet" href="css/nav.css">
<header>
  <div class="logo"><img src="images/#.jpg" alt="Company Logo"></div>
  <nav>
    <a class="active" href="mainpage.php">Home</a>
    <a href="kutipan_bulanan.php">Kutipan</a>
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
