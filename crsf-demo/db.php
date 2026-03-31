<?php

$conn = pg_connect("host=localhost port=5432 dbname=test user=postgres password=year=2082@");

if (!$conn) {
    die("Connection failed");
}

echo "Connected successfully!";

?>