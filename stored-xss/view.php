<?php
include "db.php";

$result = pg_query($conn, "SELECT * FROM comments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Comments</title>
</head>
<body>

<h2>Comments</h2>

<?php
while($row = pg_fetch_assoc($result)) {
    echo "<p>" . $row['message'] . "</p>";
}
?>

</body>
</html>