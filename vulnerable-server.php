<?php
$search = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Page</title>
</head>
<body>

<h1>Search</h1>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search..." />
    <button type="submit">Search</button>
</form>

<hr>

<h2>
    <?php
    // ❌ VULNERABLE LINE (this causes XSS)
    echo "You searched for: $search";
    ?>
</h2>

</body>
</html>