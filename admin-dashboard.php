<?php
session_start();

$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
} else {
    header("Location: login.php"); 
    exit;
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
<head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/><title>Admin Dashboard • Lost & Found</title><link rel="stylesheet" href="styles.css"/></head>
<body>
    <script>
  <?php if (isset($_SESSION['user_email'], $_SESSION['user_role'])): ?>
    localStorage.setItem('lf_email', <?= json_encode($_SESSION['user_email']) ?>);
    localStorage.setItem('lf_role', <?= json_encode($_SESSION['user_role']) ?>);
  <?php endif; ?>
</script>
  <main class="card">
    <div class="header">
      <h1>Admin Dashboard</h1>
      <div class="actions">
        <a class="btn" href="admin-users.php">User Accounts</a>
        <a class="btn" href="admin-matches.php">Approve Matches</a>
        <a class="btn" href="admin-analytics.php">Analytics & Reports</a>
        <a class="btn" href="admin-audit.php">Audit Logs</a>
        <a class="btn" href="admin-notify.php">Notifications</a>
        <a class="btn ghost" href="logout.php">Logout</a>
      </div>
    </div>
    <section class="grid-3">
      <div class="card-mini"><h3>Items Reported</h3><div class="big">128</div><span class="badge">+8 today</span></div>
      <div class="card-mini"><h3>Items Returned</h3><div class="big">76</div><span class="badge ok">59%</span></div>
      <div class="card-mini"><h3>Pending Matches</h3><div class="big">12</div><span class="badge warn">Action needed</span></div>
    </section>
    <h2 style="margin-top:22px">Recent Activity</h2>
    <table class="table">
      <thead><tr><th>When</th><th>Event</th><th>Actor</th><th>Status</th></tr></thead>
      <tbody>
        <tr><td>Today 10:12</td><td>Override match L#245</td><td>Admin2</td><td><span class="badge warn">Overridden</span></td></tr>
        <tr><td>Today 09:33</td><td>New user signup</td><td>User</td><td><span class="badge ok">OK</span></td></tr>
        <tr><td>Yesterday</td><td>Failed logins (5)</td><td>IP 203.0.113.45</td><td><span class="badge err">Security</span></td></tr>
      </tbody>
    </table>
  </main>
</body>
</html>
 

