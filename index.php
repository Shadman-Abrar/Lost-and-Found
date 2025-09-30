<?php
session_start();

$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
} else {
    // no redirect on this page
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lostandfound";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Lost & Found Portal</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body class="bg-map">
  <main class="card">
    <h1>Lost &amp; Found Portal</h1>
    <p>Report, find, and recover items. Choose an option to continue.</p>
    <div class="actions">
      <a class="btn primary" href="register.php">Create Account</a>
      <a class="btn" href="login.php">Log In</a>
    </div>
  </main>
  <script>
    // Optional: if already logged in as admin, clicking User Profile goes to admin dashboard
    const r = localStorage.getItem('lf_role');
    document.querySelectorAll('a[href="profile.php"]').forEach(a=>{
      a.addEventListener('click', e=>{
        if(r==='admin'){ e.preventDefault(); window.location.href='admin-dashboard.html'; }
      });
    });
  </script>
</body>
</html>

