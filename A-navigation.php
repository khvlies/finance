<?php
session_start();
$display_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page if not logged in
if (!isset($_SESSION['admin'])) {
  header("Location: loginpage.php");
  exit();
}
?>
<link rel="stylesheet" href="../css/sidebar.css">
<header>
    <div class="menu-toggle" onclick="toggleSidebar()">
        <img src="../images/menu.png" alt="Menu Icon" />
    </div>
    <div class="logo">
        <img src="../images/logo.png" alt="Company Logo">
    </div>
</header>

<div id="sidebar" class="sidebar">
    <button class="close-btn" onclick="toggleSidebar()">×</button>
    <div class="sidebar-profile">
        <img src="../images/profile.png" alt="Profile Icon" />
        <p><?php echo htmlspecialchars($display_name); ?></p>
    </div>
    
    <nav>
        <a href="../adminpage.php">Home</a>
        <a href="../kutipan/A-kutipan.php">Kutipan</a>
        <a href="../agihan/A-agihan.php">Agihan</a>
        <a href="../amil/A-amil.php">Amil</a>
    </nav>
    <div class="sidebar-bottom">
        <a href="../logout.php">Log Out</a>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
</script>