<?php
session_start();

if (!isset($_SESSION['s_username'])) {
    // Redirect to login page if not logged in
    header("Location: loginpage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/#"/>
  <title>Main</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <?php include('navigation.php'); ?>
  <div class="content">
    <div class="row">
      <div class="column">
        <div id="Kutipan" class="box-content">
          <a href="#"><img src="images/#" alt="Icon"></a>
          <h2>Kutipan</h2>
        </div>
      </div>
      <div class="column">
        <div id="Agihan" class="box-content">
          <a href="#"><img src="images/#" alt="Icon"></a>
          <h2>Agihan</h2>
        </div>
      </div>
      <div class="column">
        <div id="Amil" class="box-content">
        <a href="#"><img src="images/#" alt="Icon"></a>
            <h2>Amil</h2>
        </div>
      </div>
  </div>
</body>
</html>
