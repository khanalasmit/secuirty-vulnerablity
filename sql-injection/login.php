<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM auth_user WHERE username = '$username' AND password = '$password'";

    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        echo "Login successful!";
    } else {
        echo "Invalid credentials";
    }
}

?>


<!DOCTYPE html>
<html>
<body>

<h2>Login</h2>

<form method="POST">
    Username: <input type="text" name="username"><br><br>
    Password: <input type="text" name="password"><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>