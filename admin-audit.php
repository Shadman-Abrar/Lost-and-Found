<?php
session_start();

// Only allow admin users
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: profile.php');
    exit;
}

// Refresh session cookie (7 days)
$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "lostandfound");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch audit logs (latest 20 entries)
$sql = "SELECT timestamp, actor, action, target, result FROM audit_logs ORDER BY timestamp DESC LIMIT 20";
$result = $conn->query($sql);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin • Audit Logs</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <main class="card">
    <div class="header">
      <h1>Audit Log Review</h1>
      <a class="btn" href="admin-dashboard.php">Back</a>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Actor</th>
          <th>Action</th>
          <th>Target</th>
          <th>Result</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $timestamp = htmlspecialchars($row['timestamp']);
            $actor = htmlspecialchars($row['actor']);
            $action = htmlspecialchars($row['action']);
            $target = htmlspecialchars($row['target']);
            $resultText = htmlspecialchars($row['result']);

            // Determine badge class based on result text (simple example)
            $badgeClass = 'badge';
            $rtLower = strtolower($resultText);
            if (strpos($rtLower, 'suspended') !== false || strpos($rtLower, 'overridden') !== false) {
                $badgeClass .= strpos($rtLower, 'suspended') !== false ? ' ok' : ' warn';
            } elseif (strpos($rtLower, 'csv') !== false) {
                $badgeClass = 'badge';
            }

            echo "<tr>
              <td>$timestamp</td>
              <td>$actor</td>
              <td>$action</td>
              <td>$target</td>
              <td><span class=\"$badgeClass\">$resultText</span></td>
            </tr>";
          }
        } else {
          echo '<tr><td colspan="5" style="text-align:center;">No audit log entries found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </main>
</body>
</html>
