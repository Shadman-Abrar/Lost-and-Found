<?php
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_email'], $_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$profileData = [
    'first' => '',
    'last' => '',
    'email' => $_SESSION['user_email'],
    'phone' => '',
    'location' => '',
    'bio' => ''
];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize input data
    $first = trim(filter_input(INPUT_POST, 'first', FILTER_SANITIZE_STRING));
    $last = trim(filter_input(INPUT_POST, 'last', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $location = trim(filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING));
    $bio = trim(filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING));

    if (!$first || !$last || !$email) {
        $error = "First name, Last name and valid Email are required.";
    } else {
        $profileData = compact('first', 'last', 'email', 'phone', 'location', 'bio');

        // Save profile data as JSON file named by md5 of email
        $filename = 'profiles/' . md5($email) . '.json';
        if (!is_dir('profiles')) {
            mkdir('profiles', 0755, true);
        }

        if (file_put_contents($filename, json_encode($profileData, JSON_PRETTY_PRINT))) {
            $success = "Profile updated successfully.";
            // Optionally update session email if changed
            $_SESSION['user_email'] = $email;
        } else {
            $error = "Failed to save profile data.";
        }
    }
} else {
    // On GET, try loading existing profile data if available
    $filename = 'profiles/' . md5($_SESSION['user_email']) . '.json';
    if (file_exists($filename)) {
        $jsonData = file_get_contents($filename);
        $loaded = json_decode($jsonData, true);
        if ($loaded) {
            $profileData = array_merge($profileData, $loaded);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Profile • Lost & Found</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<script>
  // Set localStorage for consistency with JS redirects on other pages
  localStorage.setItem('lf_email', <?= json_encode($_SESSION['user_email']) ?>);
  localStorage.setItem('lf_role', <?= json_encode($_SESSION['user_role'] ?? 'user') ?>);
</script>
<main class="card">
<div class="header">
    <h1>Profile</h1>
    <div class="actions">
        <a class="btn" href="user-dashboard.html">Dashboard</a>
        <a class="btn" href="report-lost.html">Report Lost</a>
        <a class="btn" href="report-found.html">Report Found</a>
        <a class="btn" href="view-lost.html">View Lost</a>
        <a class="btn" href="view-found.html">View Found</a>
        <a class="btn" href="change-password.html">Change Password</a>
        <a class="btn ghost" href="logout.php">Logout</a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert" style="color: red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert" style="color: green;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<h2 style="margin-top:22px">View / Update Details</h2>
<form method="POST" action="profile.php" id="profileForm">
    <div class="row">
        <div class="input"><input name="first" placeholder="First name" required value="<?= htmlspecialchars($profileData['first']) ?>" /></div>
        <div class="input"><input name="last" placeholder="Last name" required value="<?= htmlspecialchars($profileData['last']) ?>" /></div>
    </div>
    <div class="row">
        <div class="input"><input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($profileData['email']) ?>" /></div>
        <div class="input"><input name="phone" placeholder="Phone" value="<?= htmlspecialchars($profileData['phone']) ?>" /></div>
    </div>
    <div class="input"><input name="location" placeholder="City / Campus" value="<?= htmlspecialchars($profileData['location']) ?>" /></div>
    <div class="input"><textarea name="bio" rows="3" placeholder="About"><?= htmlspecialchars($profileData['bio']) ?></textarea></div>
    <div class="actions"><button class="btn primary" type="submit">Save Changes</button><a class="btn" href="index.html">Home</a></div>
</form>
</main>
</body>
</html>
