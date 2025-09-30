<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: profile.php');
    exit;
}

// Set cookie lifetime (7 days)
$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
}

$conn = new mysqli("localhost", "root", "", "lostandfound");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$viewedUser = null;
$message = '';

// Handle POST actions: view, deactivate, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id'] ?? 0);

    if ($userId > 0) {
        if (isset($_POST['view'])) {
            // Fetch details of the user for viewing
            $stmt = $conn->prepare("SELECT id, first, last, email, status FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $viewedUser = $result->fetch_assoc();
            $stmt->close();
        } elseif (isset($_POST['deactivate'])) {
            // Set user status to Inactive
            $stmt = $conn->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                $message = "User ID $userId deactivated.";
            } else {
                $message = "Failed to deactivate user ID $userId.";
            }
            $stmt->close();
        } elseif (isset($_POST['delete'])) {
            // Delete the user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            if ($stmt->execute()) {
                $message = "User ID $userId deleted.";
            } else {
                $message = "Failed to delete user ID $userId.";
            }
            $stmt->close();
        }
    }
}

// Fetch list of users
$sql = "SELECT id, first, last, email, status FROM users ORDER BY id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin • Users</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <main class="card">
    <div class="header">
      <h1>User Accounts</h1>
      <a class="btn" href="admin-dashboard.php">Back</a>
    </div>

    <?php if ($message): ?>
      <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($viewedUser): ?>
      <section class="card-mini" style="margin-bottom: 1em;">
        <h2>User Details</h2>
        <p><strong>ID:</strong> <?= htmlspecialchars($viewedUser['id']) ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($viewedUser['first'] . ' ' . $viewedUser['last']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($viewedUser['email']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($viewedUser['status']) ?></p>
      </section>
    <?php endif; ?>

    <table class="table">
      <thead>
        <tr><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $fullName = htmlspecialchars($row['first'] . ' ' . $row['last']);
                $email = htmlspecialchars($row['email']);
                $statusText = htmlspecialchars($row['status']);
                $statusClass = ($statusText === 'Active') ? 'badge ok' : 'badge bad';

                echo '<tr>';
                echo "<td>$fullName</td>";
                echo "<td>$email</td>";
                echo "<td><span class=\"$statusClass\">$statusText</span></td>";
                echo '<td class="actions">';
                echo '<form method="POST" style="display:inline;margin-right:5px;">';
                echo '<input type="hidden" name="user_id" value="' . (int)$row['id'] . '"/>';
                echo '<button class="btn" type="submit" name="view">View</button>';
                echo '</form>';

                // Disable deactivate button if already inactive
                if ($statusText === 'Active') {
                    echo '<form method="POST" style="display:inline;margin-right:5px;">';
                    echo '<input type="hidden" name="user_id" value="' . (int)$row['id'] . '"/>';
                    echo '<button class="btn" type="submit" name="deactivate">Deactivate</button>';
                    echo '</form>';
                } else {
                    echo '<button class="btn" disabled style="margin-right:5px;">Deactivate</button>';
                }

                // Delete button - always enabled
                echo '<form method="POST" style="display:inline;">';
                echo '<input type="hidden" name="user_id" value="' . (int)$row['id'] . '"/>';
                echo '<button class="btn" type="submit" name="delete" onclick="return confirm(\'Are you sure to delete this user?\');">Delete</button>';
                echo '</form>';

                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" style="text-align:center;">No users found.</td></tr>';
        }
        $conn->close();
        ?>
      </tbody>
    </table>
  </main>
</body>
</html>
