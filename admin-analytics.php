<?php
session_start();

// Restrict page to admin users only
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

// Fetch total items count
$totalItems = $conn->query("SELECT COUNT(*) FROM items")->fetch_row()[0] ?? 0;

// Fetch found items count
$foundItems = $conn->query("SELECT COUNT(*) FROM found_items")->fetch_row()[0] ?? 0;

// Fetch returned items count
$returnedItems = $conn->query("SELECT COUNT(*) FROM returned_items")->fetch_row()[0] ?? 0;

// Fetch recent security events (limit to 5 latest)
$securitySql = "SELECT event_date, event_type, count, notes FROM security_events ORDER BY event_date DESC LIMIT 5";
$securityResult = $conn->query($securitySql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin • Analytics & Reports</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <main class="card">
    <div class="header">
      <h1>Analytics &amp; Reporting</h1>
      <a class="btn" href="admin-dashboard.php">Back</a>
    </div>

    <section class="grid-3">
      <div class="card-mini">
        <h3>Total Items</h3>
        <div class="big"><?= htmlspecialchars($totalItems) ?></div>
      </div>
      <div class="card-mini">
        <h3>Found</h3>
        <div class="big"><?= htmlspecialchars($foundItems) ?></div>
      </div>
      <div class="card-mini">
        <h3>Returned</h3>
        <div class="big"><?= htmlspecialchars($returnedItems) ?></div>
      </div>
    </section>

    <h2 style="margin-top:22px">Reports</h2>
    <div class="actions">
      <button class="btn">Download CSV (by date)</button>
      <button class="btn">Download CSV (by category)</button>
      <button class="btn">Download CSV (by location)</button>
      <button class="btn">Security Events CSV</button>
    </div>

    <h2 style="margin-top:22px">Recent Security Signals</h2>
    <table class="table">
      <thead>
        <tr><th>Time</th><th>Event</th><th>Count</th><th>Notes</th></tr>
      </thead>
      <tbody>
        <?php
        if ($securityResult && $securityResult->num_rows > 0) {
            while ($row = $securityResult->fetch_assoc()) {
                $time = htmlspecialchars($row['event_date']);
                $event = htmlspecialchars($row['event_type']);
                $count = (int)$row['count'];
                $notes = htmlspecialchars($row['notes']);
                $badgeClass = 'badge';

                if (stripos($notes, 'investigate') !== false) {
                    $badgeClass .= ' err';
                } elseif (stripos($notes, 'reviewed') !== false) {
                    $badgeClass .= ' warn';
                }

                echo "<tr>
                      <td>$time</td>
                      <td>$event</td>
                      <td>$count</td>
                      <td><span class=\"$badgeClass\">$notes</span></td>
                    </tr>";
            }
        } else {
            echo '<tr><td colspan="4" style="text-align:center;">No security events found.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </main>
</body>
</html>
