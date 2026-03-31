<!DOCTYPE html>
<html>
<head>
    <title>Stored XSS Demo</title>
</head>
<body>

<h2>Leave a Comment</h2>

<form action="store.php" method="POST">
    <textarea name="message" rows="4" cols="40"></textarea><br><br>
    <button type="submit">Submit</button>
</form>

<a href="view.php">View Comments</a>

</body>
</html>