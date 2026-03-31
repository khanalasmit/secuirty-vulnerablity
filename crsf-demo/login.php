<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $res = pg_query($conn,
        "SELECT * FROM users WHERE username='$u' AND password='$p'"
    );

    if (pg_num_rows($res) > 0) {
        $user = pg_fetch_assoc($res);
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Login failed";
    }
}
?>

<form method="POST">
<input name="username">
<input name="password">
<button>Login</button>
</form>

