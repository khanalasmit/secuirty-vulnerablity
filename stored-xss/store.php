<?php

include "db.php";

$message = $_POST['message'];

$query = "INSERT INTO comments (message) VALUES ('$message')";
pg_query($conn, $query);

header("Location: view.php");
exit();

?>