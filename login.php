<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$role) {
        $error = "Please select a role.";
    } else {
        $conn = new mysqli("localhost", "root", "", "lostandfound");
        if ($conn->connect_error) die("DB connection failed!");

        $table = ($role === 'admin') ? "admin" : "users";

        $stmt = $conn->prepare("SELECT id, password FROM {$table} WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $dbPassword);
            $stmt->fetch();

            if ($password === $dbPassword) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_role'] = $role;
                $_SESSION['user_email'] = $email;

                if ($role === 'admin') {
                    header("Location: admin-dashboard.php");
                } else {
                    header("Location: profile.php");
                }
                exit();
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "Invalid credentials.";
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login • Lost & Found</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body class="bg-map">
  <main class="card" style="max-width:560px">
    <h1>Log In</h1>
    <?php if ($error): ?>
      <div class="alert"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="POST" id="loginForm" autocomplete="on" novalidate>
      <div class="input"><span class="icon">📧</span>
        <input type="email" name="email" placeholder="Email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>" />
      </div>
      <div class="input"><span class="icon">🛂</span>
        <select id="role" name="role" required>
          <option value="">Select role</option>
          <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
          <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>
      <div class="input"><span class="icon">🔒</span>
        <input type="password" name="password" placeholder="Password" required />
      </div>
      <div class="helper">
        <label><input type="checkbox" /> Remember me</label>
        <a href="forgot-password.html">Forgot password?</a>
      </div>
      <div class="actions">
        <button class="btn primary" type="submit">Log In</button>
        <a class="btn" href="index.php">Back</a>
      </div>
      <div class="helper">New here? <a href="register.php">Create an account</a></div>
    </form>
  </main>
  <script>
    document.getElementById('loginForm').addEventListener('submit', function(e){
      const email=this.email.value.trim(), role=this.role.value;
      if(!email || !role){
        e.preventDefault();
        alert('Please fill all fields.');
        return false;
      }
    });
  </script>
</body>
</html>
