<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
</head>
<body>
    <?php include(HEADER_PATH) ?>

    <?php if($_SESSION["role_id"] > 1): ?>
        <p><a href="book-create.php">Add a new book</a></p>
    <?php endif ?>
</body>
</html>