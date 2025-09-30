<?php
session_start();

// Only allow admin access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: profile.php');
    exit;
}

// Refresh session cookie (7 days)
$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
}

// Initialize variables for form feedback
$success = false;
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audience = $_POST['audience'] ?? '';
    $type = $_POST['type'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$audience || !$type || !$subject || !$message) {
        $error = 'Please fill in all required fields.';
    } else {
        $conn = new mysqli("localhost", "root", "", "lostandfound");
        if ($conn->connect_error) {
            $error = "Database connection failed: " . $conn->connect_error;
        } else {
            $stmt = $conn->prepare("INSERT INTO notifications (audience, type, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $audience, $type, $subject, $message);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = "Failed to send notification.";
            }
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin • Notifications</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
<main class="card">
  <div class="header">
    <h1>Send Notification / Announcement</h1>
    <a class="btn" href="admin-dashboard.php">Back</a>
  </div>

  <?php if ($success): ?>
    <div class="alert ok">Notification queued successfully.</div>
  <?php elseif ($error): ?>
    <div class="alert err"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form class="form" id="notifyForm" method="POST" novalidate>
    <div class="row">
      <div class="input">
        <span class="icon">🎯</span>
        <select name="audience" required>
          <option value="">Audience</option>
          <option value="All Users" <?= (($_POST['audience'] ?? '') === 'All Users') ? 'selected' : '' ?>>All Users</option>
          <option value="Only Finders" <?= (($_POST['audience'] ?? '') === 'Only Finders') ? 'selected' : '' ?>>Only Finders</option>
          <option value="Only Claimants" <?= (($_POST['audience'] ?? '') === 'Only Claimants') ? 'selected' : '' ?>>Only Claimants</option>
        </select>
      </div>
      <div class="input">
        <span class="icon">🔔</span>
        <select name="type" required>
          <option value="">Type</option>
          <option value="Announcement" <?= (($_POST['type'] ?? '') === 'Announcement') ? 'selected' : '' ?>>Announcement</option>
          <option value="Security" <?= (($_POST['type'] ?? '') === 'Security') ? 'selected' : '' ?>>Security</option>
          <option value="Service Update" <?= (($_POST['type'] ?? '') === 'Service Update') ? 'selected' : '' ?>>Service Update</option>
        </select>
      </div>
    </div>
    <div class="input">
      <span class="icon">✉️</span>
      <input name="subject" placeholder="Subject" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" />
    </div>
    <div class="input">
      <span class="icon">📝</span>
      <textarea name="message" rows="5" placeholder="Message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
    </div>
    <div class="actions">
      <button class="btn primary" type="submit">Send</button>
      <button class="btn" type="reset">Clear</button>
    </div>
  </form>
</main>
</body>
</html>
