<?php
session_start();

// Allow access only to admin users
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: profile.php');
    exit;
}

// Refresh session cookie to keep login alive (7 days)
$cookie_lifetime = 86400 * 7;
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $cookie_lifetime, "/");
}

// Connect to MySQL database
$conn = new mysqli("localhost", "root", "", "lostandfound");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Approve or Override actions on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchId = $_POST['match_id'] ?? '';
    if ($matchId) {
        if (isset($_POST['approve'])) {
            $stmt = $conn->prepare("UPDATE matches SET status='approved' WHERE match_id=?");
        } elseif (isset($_POST['override'])) {
            $stmt = $conn->prepare("UPDATE matches SET status='overridden' WHERE match_id=?");
        }
        if (isset($stmt)) {
            $stmt->bind_param("s", $matchId);
            $stmt->execute();
            $stmt->close();
        }
    }
    // Redirect to prevent resubmit on refresh
    header("Location: admin-matches.php");
    exit;
}

// Fetch all matches ordered by recent
$sql = "SELECT match_id, item_title, claimant_name, similarity_percentage, status FROM matches ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin • Matches</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <main class="card">
        <div class="header">
            <h1>Approve / Override Matches</h1>
            <a class="btn" href="admin-dashboard.php">Back</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Match ID</th>
                    <th>Item</th>
                    <th>Claimant</th>
                    <th>Similarity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $matchId = htmlspecialchars($row['match_id']);
                    $itemTitle = htmlspecialchars($row['item_title']);
                    $claimant = htmlspecialchars($row['claimant_name']);
                    $similarity = (int)$row['similarity_percentage'] . '%';
                    $status = htmlspecialchars(ucfirst($row['status']));
                    $statusClass = 'badge';
                    if ($row['status'] === 'approved') $statusClass .= ' ok';
                    elseif ($row['status'] === 'pending') $statusClass .= ' warn';
                    elseif ($row['status'] === 'overridden') $statusClass .= ' bad';

                    echo '<tr>';
                    echo "<td>$matchId</td>";
                    echo "<td>$itemTitle</td>";
                    echo "<td>$claimant</td>";
                    echo "<td>$similarity</td>";
                    echo "<td><span class=\"$statusClass\">$status</span></td>";
                    echo '<td class="actions">';
                    if ($row['status'] === 'pending') {
                        echo '<form method="POST" style="display:inline">';
                        echo '<input type="hidden" name="match_id" value="' . $matchId . '"/>';
                        echo '<button type="submit" name="approve" class="btn primary">Approve</button>';
                        echo '</form> ';
                        echo '<form method="POST" style="display:inline">';
                        echo '<input type="hidden" name="match_id" value="' . $matchId . '"/>';
                        echo '<button type="submit" name="override" class="btn">Override</button>';
                        echo '</form>';
                    } else {
                        echo '-';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">No matches found.</td></tr>';
            }
            $conn->close();
            ?>
            </tbody>
        </table>
    </main>
</body>
</html>
