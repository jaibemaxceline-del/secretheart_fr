<?php
session_start();
 
require_once "config/database.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Check required fields
    if ($username === "" || $password === "") {
        $message = "Please fill in all fields.";
    }

    else {
        // Check whether username exists
        $stmt = $pdo->prepare(
            "SELECT user_id, username, password, role, status
             FROM users
             WHERE username = ?"
        );

        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
   // Verify password
              $message = "invalid username or password.";

        } elseif (!password_verify($password, $user["password"])) {
            $message = "Invalid username or password.";

        } elseif ($user["status"] !=="Active") {
            $message = "Your account is not active.";

        } else{
            session_regenerate_id(true);

            $_session["user_id"] = $user["user_id"];
            $_session["username"] =$user["username"];
            $_session["email"] =$user["email"];
            $_session["role"] =$user["role"];

            if($user["role"]==="Admin"){
                header("Location:Admin/dashboard.php");
                exit;
            }elseif($user["role"]==="librarian"){

            header("Location: Librarian/dashboard.php");
            exit;

            }else{
                header("Location: member/dashboard.php");
                exit;
            }

        }
    }
}
?>

<!Doctype html>
<html lang = "en">
    <head>
        <meta charset="UTF-8">
        <meta name = "viewport" = "width, initial-scale=1.0">
        <title> login</title>
    </head>
    <body>
        <h2>Jean Dehon Library</h2> 
        <h3>login</h3>
        <?php if ($message !==""): ?>
            <p>
                <?php echo htmlspecialcharS($message); ?>
            </p>
            <?php endif; ?>
            
            <form method="POST">
                <label>Useername</label><br>
                <input type = "text" name=" username" require> <br><br>

                 <input type = "password" name="password" require> <br><br>
                 <button type = "submit">Login</button>
            </form>

            <p>
                Don't have an account?
                <a href = "register.php">Register</a>
            </p>
    </body>
</html>
         
            
               