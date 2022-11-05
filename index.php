<?php
    require("connect.php");
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Keepers</title>
</head>
<body>
    <?php require("header.php"); ?>
    <?php print_r($_SESSION); ?>
</body>
</html>