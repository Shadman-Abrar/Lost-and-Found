<?php
session_start();
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first = trim($_POST['first'] ?? '');
    $last = trim($_POST['last'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $agreed = isset($_POST['agree']);

    if ($first === '') $errors[] = "First name is required.";
    if ($last === '') $errors[] = "Last name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";
    if (!$agreed) $errors[] = "You must accept the terms.";

    if (count($errors) === 0) {
        $conn = new mysqli("localhost", "root", "", "lostandfound");
        if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

        // Check for duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered.";
        } else {
            // Store password in plain text (NOT recommended)
            $stmt = $conn->prepare("INSERT INTO users (first, last, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $first, $last, $email, $password);
            if ($stmt->execute()) {
                $success = true;
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Registration failed.";
            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-map">
    <main class="card">
        <h1>Create Account</h1>
        <?php if ($errors): ?>
            <div class="alert">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
            </div>
        <?php endif; ?>
        <form class="form" method="POST" id="regForm" autocomplete="on" novalidate>
            <div class="row">
                <div class="input"><input name="first" placeholder="First name" required value="<?=htmlspecialchars($_POST['first'] ?? '')?>"></div>
                <div class="input"><input name="last" placeholder="Last name" required value="<?=htmlspecialchars($_POST['last'] ?? '')?>"></div>
            </div>
            <div class="input"><input type="email" name="email" placeholder="Email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>"></div>
            <div class="row">
                <div class="input"><input type="password" name="password" placeholder="Password" minlength="8" required></div>
                <div class="input"><input type="password" name="confirm" placeholder="Confirm password" required></div>
            </div>
            <div class="helper"><label><input type="checkbox" name="agree" required <?=isset($_POST['agree'])?"checked":""?>> I agree to terms</label></div>
            <div class="actions"><button class="btn primary" type="submit">Sign Up</button><a class="btn" href="login.php">Have an account? Log in</a></div>
        </form>
    </main>
</body>
</html>
