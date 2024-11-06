<?php
    require("constants.php");
    require(CONNECT_PATH);
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookkeeper</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= STYLE_PATH ?>" rel="stylesheet">
</head>
<body>
    <?php require(HEADER_PATH); ?>
    <?php print_r($_SESSION); ?>
</body>
</html>