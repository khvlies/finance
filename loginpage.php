<?php
  if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
    <p style="color: red;">Invalid username or password.</p>
  <?php endif;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/icon.png"/>
  <title>Welcome</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="title">FINANCIAL STATEMENT</div>
  <div class="container">
    <div class="logo">
      <img src="images/finance.png" alt="Company Logo" style="width: 140px; height: auto;">
    </div>
    <form id="login-form" action="loginSession.php" method="post">
      <input type="text" name="un" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Login">
    </form>
    <p>
    <a href="#">Forgot password?</a>
    </p>
  </div>
  <footer>
    &copy;2024 Copyright Reserved
  </footer>
</body>
</html>
