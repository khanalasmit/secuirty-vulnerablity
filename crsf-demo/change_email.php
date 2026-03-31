<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$email = $_POST['email'];
$user_id = $_SESSION['user_id'];

pg_query($conn,
    "UPDATE users SET email='$email' WHERE id=$user_id"
);

echo "Email changed!";
?>