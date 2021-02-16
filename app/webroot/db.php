<?php

$link = mysqli_connect(getenv("DB_HOST"), getenv("DB_USER"), getenv("DB_PASS"));

if (!$link) {
  die('Could not connect error number: ' . mysqli_connect_errno());
}

$res = mysqli_query($link, "SHOW DATABASES");
if (!$res) { 
    printf("Error message: %s\n", mysqli_error($link)); 
}

foreach ($res as $row) {
    echo $row["Database"], "<br />";
}

mysqli_close($link);