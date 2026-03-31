<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Login required");
}
?>

<h2>Welcome</h2>

<form action="change_email.php" method="POST">
    New Email: <input name="email">
    <button>Change Email</button>
</form>