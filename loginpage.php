
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="../images/f-logo.png"  type="image/png">
  <title>Welcome</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="title">FINANCIAL STATEMENT</div>
  <div class="container">
    <div class="logo">
      <img src="images/finance.png" alt="Company Logo" style="width: 140px; height: auto;">
    </div>
    <form id="login-form" action="logintest.php" method="post">
    <?php
      session_start();
      if (isset($_SESSION['error'])): ?>
          <p style="color: red;"><?= htmlspecialchars($_SESSION['error']); ?></p>
      <?php
          unset($_SESSION['error']); // Clear the error after displaying
      endif;
      ?>
      <input type="text" name="un" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Login">
    </form>
  </div>
      

  <footer>
    &copy;2024 Copyright Reserved
  </footer>
</body>
</html>
