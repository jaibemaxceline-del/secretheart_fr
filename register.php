<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check required fields
    if ($username === "" || $email === "" || $password === "") {
        $message = "Please fill in all required fields.";
    }

    // Check email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    }

    // Check password
    elseif (strlen($password) < 6) {
        $message = "Password must contain at least 6 characters.";
    }

    // Check passwords
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    }

    else {

        // Check whether username or email already exists
        $check = $pdo->prepare(
            "SELECT user_id FROM users
             WHERE username = ? OR email = ?"
        );

        $check->execute([$username, $email]);

        if ($check->fetch()) {
            $message = "Username or email already exists.";
        }

        else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert member
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (username, password, email, phone, role, status)
                VALUES (?, ?, ?, ?, 'Member', 'Active')"
            );

            $stmt->execute([
                $username,
                $hashed_password,
                $email,
                $phone
            ]);

            $message = "Registration successful! You can now login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Secret Heart Library</title>
</head>

<body>

    <h2>Library Registration</h2>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Username </label><br>
        <input type="text" name="username" required><br><br>

        <label>Email </label><br> <input type="email" name="email" required><br><br>

        <label>Phone</label><br> <input type="text" name="phone"><br><br>

        <label>Password </label><br> <div class="password-container">
    <input type="password" name="password" id="password" required>
    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
👁
</button></div><br><br>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        button.textContent = "🙈";
    } else {
        input.type = "password";
        button.textContent = "👁";
    }
}
</script>

        <label>Confirm Password </label><br><input type="password" name="confirm_password" required><br><br>

        <button type="submit">Register</button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</body>
</html>