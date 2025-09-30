<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Logged Out</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body class="bg-map">
  <main class="card" style="max-width:560px">
    <h1>Logged Out</h1>
    <p>Session ended successfully.</p>
    <div class="actions">
      <a class="btn primary" href="login.php">Log In Again</a>
      <a class="btn" href="index.php">Home</a>
    </div>
  </main>
  <script>
    localStorage.removeItem('lf_role');
    localStorage.removeItem('lf_email');
  </script>
</body>
</html>
