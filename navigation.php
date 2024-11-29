<?php
session_start();
$display_name = isset($_SESSION['staff_name']) ? $_SESSION['staff_name'] : 
                (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'User');

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page if not logged in
if (!isset($_SESSION['staff']) && !isset($_SESSION['admin'])) {
  header("Location: loginpage.php");
  exit();
}
?>
<link rel="stylesheet" href="../css/nav.css">
<header>
<div class="logo"><img src="../images/#.jpg" alt="Company Logo"></div>
  <nav>
    <a href="../mainpage.php">Home</a>
    <a href="../filter.php">Kutipan</a>
    <a href="#.php">Agih</a>
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