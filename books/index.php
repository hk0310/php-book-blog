<?php
    require(".." . DIRECTORY_SEPARATOR . "constants.php");
    require(CONNECT_PATH);
    session_start();

    $query = "SELECT * FROM Books";
    $statement = $db->prepare($query);
    $statement->execute();
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

    <?php while($row = $statement->fetch()): ?>
        <?php if($row['cover_image_path']): ?>
            <img src="<?= $row['cover_image_path'] ?>" alt="<?= $row['title'] ?>_cover">
        <?php else: ?>
            <img src="<?= NO_COVER_PATH ?>" alt="no_cover">
        <?php endif ?>
        <p><?= $row['title'] ?></p>
        <p>By <?= $row['author'] ?></p>
        <?php if($_SESSION['role_id'] > 1): ?>
            <p><a href="book-edit.php?id=<?= $row['book_id'] ?>">Make Changes</a></p>
        <?php endif ?>
    <?php endwhile ?>
</body>
</html>